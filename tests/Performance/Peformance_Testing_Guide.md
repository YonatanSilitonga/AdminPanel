# Panduan Performance Testing — K6 Load Testing

Dokumen ini menjelaskan seluruh file performance test yang ada di folder `tests/Performance/`, cara kerja masing-masing, dan cara menjalankannya.

---

## Gambaran Umum Arsitektur Testing

Semua file test menggunakan **[K6](https://k6.io/)** — sebuah tool open-source untuk load testing berbasis JavaScript yang dirancang untuk simulasi pengguna nyata (Virtual Users / VU) yang mengakses sistem secara bersamaan.

```
tests/
└── Performance/
    ├── utils/
    │   └── auth_helper.js          ← Utilitas login (CSRF + Session)
    ├── 1_auth_test.js              ← [01] Login & Autentikasi
    ├── 2_dashboard_analytics_test.js  ← [02] Dashboard & Chart AJAX
    ├── 2b_dashboard_simple_test.js    ← [02b] Dashboard Ringan
    ├── 3_content_management_test.js   ← [03] Manajemen Konten (semua modul)
    ├── 4_moderation_logs_test.js      ← [04] Moderasi & Log
    ├── 5_destinations_crud_test.js    ← [05] Destinasi (Read-only)
    ├── 6_analytics_test.js            ← [06] Analytics (semua endpoint)
    ├── 7_reviews_detail_test.js       ← [07] Ulasan & Sentimen
    ├── 8_reports_chatbot_detail_test.js ← [08] Laporan & Chatbot
    ├── 9_settings_profile_test.js     ← [09] Pengaturan & Profil
    ├── 10_users_activity_test.js      ← [10] Manajemen Pengguna
    └── 11_events_content_crud_test.js ← [11] Events + Content (shared session)
```

---

## Prasyarat Sebelum Menjalankan Test

### 1. Install K6
```bash
# Windows (via Chocolatey)
choco install k6

# Windows (via Winget)
winget install k6

# Verifikasi instalasi
k6 version
```

### 2. Jalankan Laravel Server
```bash
# Di folder root project (AdminPanel/)
php artisan serve
# Server akan berjalan di http://127.0.0.1:8000
```

### 3. Pastikan Database & Seeder sudah berjalan
```bash
php artisan db:seed
# Admin default: superadmin@smarttourism.local / SuperAdmin@123
```

---

## Penjelasan Utility: `utils/auth_helper.js`

File ini adalah fondasi dari semua test. Menyediakan **2 fungsi login** yang dapat dipilih sesuai kebutuhan:

| Fungsi | Cara Kerja | Kapan Digunakan |
| :--- | :--- | :--- |
| `loginAdmin()` | Setiap Virtual User (VU) login sendiri-sendiri | Test dengan VU rendah (< 10 VU) |
| `getSessionCookies()` | Login 1x di `setup()`, cookie dibagikan ke semua VU | Test dengan VU tinggi (10+ VU), menghindari session race condition |

**Alur kerja `loginAdmin()`:**
1. `GET /admin/login` → mengambil halaman login + CSRF token dari HTML response
2. Regex extract token dari atribut `name="_token" value="..."`
3. `POST /admin/login` dengan email, password, dan `_token`
4. Verifikasi redirect berhasil ke `/admin/dashboard`

---

## Penjelasan Per File Test

### `1_auth_test.js` — Pengujian Autentikasi
**Controller yang diuji:** `AdminAuthController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users (VU) | 10 |
| Durasi | 40 detik (10s ramp-up + 20s sustain + 10s ramp-down) |
| Threshold Response | 95% request < **1000ms** |
| Threshold Error | Error rate < **1%** |

**Endpoint yang diuji:**
- `GET /admin/login` — memuat halaman login publik
- `POST /admin/login` — proses autentikasi dan redirect ke dashboard

**Cara kerja khusus:** Setiap iterasi VU melakukan login baru dari awal (tanpa shared session), cocok untuk mengukur **beban server saat banyak admin login bersamaan**.

---

### `2_dashboard_analytics_test.js` — Dashboard Lengkap
**Controller yang diuji:** `DashboardController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 10 |
| Durasi | 50 detik |
| Threshold Response | 95% < **3000ms** (lebih longgar karena AJAX berat) |

**Endpoint yang diuji:**
- `GET /admin/dashboard` — halaman utama dashboard
- `GET /admin/dashboard/chart-data` — AJAX endpoint chart 12 bulan (**84 DB queries**)

> ⚠️ Endpoint `chart-data` adalah endpoint terberat di seluruh sistem karena melakukan 84 kueri MongoDB untuk mengagregasi data bulanan. Threshold-nya sengaja dilonggarkan ke 3 detik.

---

### `2b_dashboard_simple_test.js` — Dashboard Ringan
**Controller yang diuji:** `DashboardController` (versi ringkas)

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 5 |
| Durasi | 40 detik |
| Threshold Response | 95% < **2000ms** |

**Endpoint yang diuji:**
- `GET /admin/dashboard` — dashboard tanpa chart AJAX

**Tujuan:** Mengisolasi masalah performa; jika `2_dashboard_analytics_test.js` gagal, jalankan file ini untuk memastikan apakah masalahnya ada di dashboard itu sendiri atau di endpoint chart-data.

---

### `3_content_management_test.js` — Manajemen Konten
**Controller yang diuji:** `DestinationController`, `EventController`, `BudayaController`, `FasilitasUmumController`, `BeritaPromosiController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 10 |
| Durasi | 50 detik |
| Threshold Response | 95% < **600ms** (paling ketat) |

**Endpoint yang diuji secara loop:**
```
/admin/destinations     → Daftar destinasi
/admin/events           → Daftar acara/event
/admin/budaya           → Daftar budaya & warisan
/admin/fasilitas-umum   → Daftar fasilitas umum
/admin/berita-promosi   → Daftar berita & promosi
```

**Catatan penting:** Endpoint `/admin/carousel-banners` dan `/admin/search` sengaja dinonaktifkan karena route-nya tidak terdaftar di `routes/web.php`.

---

### `4_moderation_logs_test.js` — Moderasi & Log
**Controller yang diuji:** `ReviewController`, `ReportController`, `UserController`, `RecommendationLogController`, `ChatbotLogController`, `AuditLogController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 10 |
| Durasi | 50 detik |
| Threshold Response | 95% < **1000ms** |

**Endpoint yang diuji:**

| Endpoint | Role Dibutuhkan | Keterangan |
| :--- | :--- | :--- |
| `/admin/reviews` | admin, moderator, super_admin | Daftar ulasan |
| `/admin/reports` | moderator, admin, super_admin | Daftar laporan |
| `/admin/users` | admin, super_admin | Daftar pengguna |
| `/admin/recommendations` | admin, super_admin | Log rekomendasi AI (query MongoDB berat) |
| `/admin/chatbot-logs` | admin, moderator, super_admin | Log chatbot (query MongoDB berat) |
| `/admin/settings/audit-logs` | **super_admin** | Audit trail (mungkin return 403 jika bukan super_admin) |

**Catatan:** Test ini toleran terhadap HTTP 403 untuk endpoint `audit-logs` jika menggunakan credentials yang bukan super_admin.

---

### `5_destinations_crud_test.js` — Destinasi (Read-Only)
**Controller yang diuji:** `DestinationController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 10 |
| Durasi | 50 detik |
| Threshold Response | 95% < **1000ms** |

**Endpoint yang diuji:**
- `GET /admin/destinations?page=1` — halaman pertama daftar destinasi
- `GET /admin/destinations?page=2` — uji pagination
- `GET /admin/destinations/create` — form tambah destinasi (tanpa submit)

**Fitur khusus:** File ini memiliki fungsi `setup()` dan `teardown()` untuk mencatat waktu total eksekusi test.

> ✅ **AMAN untuk production** — tidak ada operasi tulis/hapus data.

---

### `6_analytics_test.js` — Analytics Menyeluruh
**Controller yang diuji:** `AnalyticsController`, `ReviewController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 5 (lebih sedikit karena query berat) |
| Durasi | 40 detik |
| Threshold Response | 95% < **4000ms** (paling longgar — aggregation berat) |

**Endpoint yang diuji:**
```
GET /admin/dashboard/chart-data        → AJAX chart 12 bulan (84 queries)
GET /admin/analytics                   → Dashboard analytics
GET /admin/analytics/destinations      → Analitik destinasi
GET /admin/analytics/events            → Analitik acara
GET /admin/analytics/reports           → Analitik laporan
GET /admin/reviews/summary/stats       → Statistik ringkasan ulasan
```

---

### `7_reviews_detail_test.js` — Ulasan & Sentimen
**Controller yang diuji:** `ReviewController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 5 |
| Durasi | 40 detik |
| Threshold Response | 95% < **2000ms** |

**Endpoint yang diuji:**
```
GET /admin/reviews                  → Daftar ulasan
GET /admin/reviews?status=pending   → Filter ulasan pending
GET /admin/reviews/summary/stats    → Statistik agregasi
GET /admin/reviews/analytics/print  → Halaman cetak analitik
```

> ⚠️ `POST /analyze`, `PATCH /approve`, `PATCH /reject` **tidak dijalankan** untuk melindungi data produksi.

---

### `8_reports_chatbot_detail_test.js` — Laporan & Chatbot
**Controller yang diuji:** `ReportController`, `ChatbotLogController`, `RecommendationLogController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 5 |
| Durasi | 40 detik |
| Threshold Response | 95% < **3000ms** |
| Threshold Error | Error rate < **5%** (lebih toleran — beberapa ID mungkin tidak ada di DB) |

**Endpoint yang diuji:**
```
GET /admin/reports                      → Daftar laporan
GET /admin/chatbot-logs                 → Daftar sesi chatbot
GET /admin/chatbot-logs?page=2          → Pagination chatbot logs
GET /admin/recommendations              → Daftar log rekomendasi
GET /admin/recommendations/export       → Export CSV (memory-intensive)
```

---

### `9_settings_profile_test.js` — Pengaturan & Profil
**Controller yang diuji:** `SettingsController`, `ProfileController`, `AuditLogController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | **3** (paling sedikit — halaman jarang diakses) |
| Durasi | 40 detik |
| Threshold Response | 95% < **2000ms** |

**Endpoint yang diuji:**
```
GET /admin/profile                      → Halaman profil admin
GET /admin/settings/general             → Pengaturan umum sistem
GET /admin/settings/audit-logs          → Daftar audit log
GET /admin/settings/audit-logs?page=2   → Pagination audit log
```

> ⚠️ Semua endpoint butuh **super_admin** credentials.

---

### `10_users_activity_test.js` — Manajemen Pengguna
**Controller yang diuji:** `UserController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 5 |
| Durasi | 40 detik |
| Threshold Response | 95% < **2000ms** |

**Endpoint yang diuji:**
```
GET /admin/users                   → Daftar pengguna
GET /admin/users?status=active     → Filter pengguna aktif
GET /admin/users?search=a          → Pencarian pengguna
GET /admin/users?page=2            → Pagination
```

> ⚠️ `PATCH /users/{id}/status` (suspend/aktifkan) **tidak dijalankan**.

---

### `11_events_content_crud_test.js` — Events & Content (Shared Session)
**Controller yang diuji:** `EventController`, `FasilitasUmumController`, `BudayaController`, `BeritaPromosiController`

| Parameter | Nilai |
| :--- | :--- |
| Virtual Users | 10 |
| Durasi | 50 detik |
| Threshold Response | 95% < **3000ms** |

**Pola unik — `getSessionCookies()` di `setup()`:**
File ini menggunakan pola yang **berbeda** dari file lainnya. Login hanya dilakukan **sekali** di fungsi `setup()`, lalu cookie session dibagikan ke semua 10 VU secara bersamaan melalui parameter `data`. Ini mencegah terjadinya *race condition* session saat banyak VU login bersamaan.

```
GET /admin/events                  → Daftar events
GET /admin/events/create           → Form tambah event
GET /admin/fasilitas-umum          → Daftar fasilitas
GET /admin/budaya                  → Daftar budaya
GET /admin/berita-promosi          → Daftar berita/promo
```

---

## Cara Menjalankan Test

### Menjalankan 1 file test
Buka terminal di folder root project, lalu jalankan perintah berikut:
```bash
# Format dasar
k6 run tests/Performance/<nama_file>.js

# Contoh: jalankan test autentikasi
k6 run tests/Performance/1_auth_test.js

# Contoh: jalankan test dashboard
k6 run tests/Performance/2b_dashboard_simple_test.js
```

### Menjalankan semua test secara berurutan (Windows PowerShell)
```powershell
# Dari folder root AdminPanel/
$testFiles = @(
    "1_auth_test.js",
    "2b_dashboard_simple_test.js",
    "3_content_management_test.js",
    "4_moderation_logs_test.js",
    "5_destinations_crud_test.js",
    "6_analytics_test.js",
    "7_reviews_detail_test.js",
    "8_reports_chatbot_detail_test.js",
    "9_settings_profile_test.js",
    "10_users_activity_test.js",
    "11_events_content_crud_test.js"
)

foreach ($file in $testFiles) {
    Write-Host "▶ Running: $file" -ForegroundColor Cyan
    k6 run "tests/Performance/$file"
    Start-Sleep -Seconds 3  # Jeda antar test
}
```

### Menjalankan dengan output ke file (untuk laporan)
```bash
# Output JSON untuk analisis lebih lanjut
k6 run tests/Performance/1_auth_test.js --out json=results/auth_test_result.json

# Output CSV
k6 run tests/Performance/1_auth_test.js --out csv=results/auth_test_result.csv
```

### Menjalankan dengan jumlah VU berbeda (override)
```bash
# Override VU dan durasi dari command line
k6 run --vus 20 --duration 60s tests/Performance/1_auth_test.js
```

---

## Membaca Output Hasil Test

Setelah test selesai, K6 akan menampilkan ringkasan seperti ini:

```
✓ GET login status is 200
✓ Page contains login form
✓ POST login successful (redirects to dashboard)

  scenarios: (100.00%) 1 scenario, 10 max VUs, 1m10s max duration
  default: Up to 10 looping VUs for 40s (gracefulStop: 30s)


    ✓ checks.........................: 100.00% ✓ 240  ✗ 0
    data_received...................: 12 MB   286 kB/s
    data_sent.......................: 872 kB  20 kB/s
    http_req_blocked................: avg=1.21ms  min=0s      med=0s      max=54ms
    http_req_duration...............: avg=352ms   min=89ms    med=294ms   max=2.1s
      { expected_response:true }....: avg=352ms   min=89ms    med=294ms   max=2.1s
  ✓ http_req_failed.................: 0.00%   ✓ 0    ✗ 240
  ✓ http_req_duration..............: p(95)=811ms  ← KUNCI: harus < threshold
    http_reqs.......................: 240     5.62/s
    iteration_duration..............: avg=4.25s
    iterations......................: 80      1.87/s
    vus.............................: 1       min=1   max=10
    vus_max.........................: 10      min=10  max=10
```

**Interpretasi metrik kunci:**

| Metrik | Arti |
| :--- | :--- |
| `http_req_duration p(95)` | 95% request selesai dalam waktu ini → **WAJIB di bawah threshold** |
| `http_req_failed` | Persentase request gagal (5xx, timeout) → **harus < 1%** |
| `checks` | Persentase assertion yang lolos → **idealnya 100%** |
| `http_reqs` | Total request per detik (throughput) |
| `iteration_duration` | Waktu rata-rata per siklus VU |

---

## Ringkasan Semua File: Referensi Cepat

| # | File | VU | Durasi | Threshold (p95) | Controller Utama |
| :- | :--- | :-: | :---: | :---: | :--- |
| 1 | `1_auth_test.js` | 10 | 40s | < 1000ms | AdminAuthController |
| 2 | `2_dashboard_analytics_test.js` | 10 | 50s | < 3000ms | DashboardController |
| 2b | `2b_dashboard_simple_test.js` | 5 | 40s | < 2000ms | DashboardController |
| 3 | `3_content_management_test.js` | 10 | 50s | < 600ms | Destination, Event, dll. |
| 4 | `4_moderation_logs_test.js` | 10 | 50s | < 1000ms | Review, Report, User |
| 5 | `5_destinations_crud_test.js` | 10 | 50s | < 1000ms | DestinationController |
| 6 | `6_analytics_test.js` | 5 | 40s | < 4000ms | AnalyticsController |
| 7 | `7_reviews_detail_test.js` | 5 | 40s | < 2000ms | ReviewController |
| 8 | `8_reports_chatbot_detail_test.js` | 5 | 40s | < 3000ms | Report, Chatbot, Rec. |
| 9 | `9_settings_profile_test.js` | 3 | 40s | < 2000ms | Settings, Profile |
| 10 | `10_users_activity_test.js` | 5 | 40s | < 2000ms | UserController |
| 11 | `11_events_content_crud_test.js` | 10 | 50s | < 3000ms | Event + Content |
