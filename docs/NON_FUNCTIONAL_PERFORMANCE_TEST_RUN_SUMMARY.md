# 📊 Ringkasan Pengujian Kinerja Non-Fungsional (K6 Performance Test Run Summary)

## 📅 Informasi Pengujian
* **Tanggal Pelaksanaan**: 10 Juni 2026
* **Penilai/Tester**: Antigravity AI Pair Programmer
* **Status Pengujian**: ✅ Sukses (0% Error Rate)
* **Kategori Pengujian**: Non-Functional Performance Testing (Load Testing)
* **Alat yang Digunakan**: K6 (v2.0.0) & PHP Laravel Built-in Server

---

## 🔒 Konsep Pengujian Tanpa Membuat Data Baru (Read-Only Testing)
Pengujian kinerja ini dirancang agar **aman dijalankan di lingkungan staging atau produksi tanpa merusak atau menambahkan data sampah (dummy data)** pada database utama.

### Mengapa pengujian ini aman dan tidak membuat data baru?
1. **Request Method GET (Read-Only)**: Hampir seluruh request dalam skrip uji di folder [tests/Performance](file:///d:/semester-4-IT%20Del/Semester%20VI/AdminPanel/AdminPanel/tests/Performance) menggunakan metode `GET` untuk memuat halaman index, detail, formulir, dan logs.
2. **Autentikasi POST Tunggal**: Metode `POST` hanya digunakan pada endpoint `/admin/login` untuk mengautentikasi pengguna simulasi (Virtual Users/VUs). Operasi ini hanya menghasilkan session/cookie baru di Laravel dan tidak menyisipkan data baru (seperti destinasi, event, atau review baru) ke dalam database MongoDB.
3. **Menggunakan Akun yang Sudah Ada**: Pengujian memanfaatkan akun administrator yang sudah didefinisikan sebelumnya di database (`superadmin@smarttourism.local` dengan sandi `SuperAdmin@123`).

---

## 🛠️ Langkah-Langkah Menjalankan Pengujian

Jika Anda ingin menjalankan kembali pengujian kinerja ini secara mandiri tanpa menambah data baru, ikuti prosedur berikut:

### 1. Jalankan Web Server Laravel
Pastikan aplikasi Laravel berjalan pada port default (`http://127.0.0.1:8000`):
```bash
php artisan serve
```

### 2. Pastikan Akun Admin Siap
Jika akun admin belum ada di database MongoDB Anda, jalankan seeder spesifik untuk akun admin saja (ini **hanya** membuat 1 user admin dan relasi perizinan tanpa membuat data konten baru seperti destinasi/review):
```bash
php artisan db:seed --class=AdminSeeder
```

### 3. Jalankan Pengujian dengan K6
Gunakan perintah `k6 run` di terminal Anda dengan mengarahkan ke file skrip pengujian spesifik di folder `tests/Performance/`:

* **Uji Autentikasi**:
  ```bash
  k6 run tests/Performance/1_auth_test.js
  ```
* **Uji Dashboard Sederhana (Simple Dashboard)**:
  ```bash
  k6 run tests/Performance/2b_dashboard_simple_test.js
  ```
* **Uji Manajemen Destinasi (Read-Only)**:
  ```bash
  k6 run tests/Performance/5_destinations_crud_test.js
  ```
* **Uji Modul Konten & Event**:
  ```bash
  k6 run tests/Performance/11_events_content_crud_test.js
  ```

---

## 📈 Hasil Eksekusi Pengujian (10 Juni 2026)

Berikut adalah ringkasan metrik kinerja dari pengujian yang baru saja dijalankan:

| Uji File & Deskripsi | Sukses Rate | Error Rate | Kinerja Durasi p(95) | Throughput (RPS) | Status Ambang Batas (Threshold) |
|----------------------|-------------|------------|-----------------------|------------------|---------------------------------|
| **Simple Dashboard** (`2b_dashboard_simple_test.js`) <br> *5 VUs, 40s duration* | **100%** (84/84 checks) | **0.00%** | **3.33 detik** | 2.01 req/s | ❌ Terlewati (Target: <2.0s) |
| **Destinations Read-Only** (`5_destinations_crud_test.js`) <br> *10 VUs, 50s duration* | **100%** (138/138 checks) | **0.00%** | **5.17 detik** | 2.20 req/s | ❌ Terlewati (Target: <1.0s) |
| **Events & Content** (`11_events_content_crud_test.js`) <br> *10 VUs, 50s duration* | **100%** (225/225 checks) | **0.00%** | **4.77 detik** | 2.14 req/s | ❌ Terlewati (Target: <3.0s) |

### 🔍 Analisis Hasil:
1. **Keandalan Tinggi (0.00% Error Rate)**: Seluruh request berhasil dimuat dengan status `200 OK` dan session login berjalan sangat stabil. Masalah autentikasi 401 yang sebelumnya terjadi telah sepenuhnya teratasi setelah seeding akun `superadmin@smarttourism.local`.
2. **Kinerja Waktu Respon Terdegradasi (Bottleneck)**: Durasi p(95) berada di kisaran 3 hingga 5 detik. Hal ini mengindikasikan adanya bottleneck pada query database MongoDB/MySQL atau pemrosesan relasi Eloquent (N+1 query) yang perlu dioptimalkan lebih lanjut menggunakan caching (Redis) dan indeks database.

---

## 📂 Struktur Dokumentasi Kinerja yang Ada
Semua dokumen panduan dan laporan pengujian kinerja disimpan secara terstruktur di dalam folder [docs/testing](file:///d:/semester-4-IT%20Del/Semester%20VI/AdminPanel/AdminPanel/docs/testing):

1. **[Laporan Utama Performance Test (NON_FUNCTIONAL_PERFORMANCE_TEST_REPORT.md)](file:///d:/semester-4-IT%20Del/Semester%20VI/AdminPanel/AdminPanel/docs/testing/NON_FUNCTIONAL_PERFORMANCE_TEST_REPORT.md)**:
   Berisi standar ISO/IEC 25010, target metrik (SLA), spesifikasi hardware/software lingkungan pengujian, skenario detail, analisis akar masalah (root cause), dan rencana aksi optimasi.
2. **[Metodologi Pengujian K6 (K6_TESTING_METHODOLOGY.md)](file:///d:/semester-4-IT%20Del/Semester%20VI/AdminPanel/AdminPanel/docs/testing/K6_TESTING_METHODOLOGY.md)**:
   Menjelaskan alasan memilih K6, pola beban (load patterns: Smoke, Stress, Spike, Soak), cara membaca metrik persentil, dan praktik terbaik (best practices) manajemen data uji.
3. **[Ringkasan & Rencana Jalan Pintas (K6_TESTING_SUMMARY.md)](file:///d:/semester-4-IT%20Del/Semester%20VI/AdminPanel/AdminPanel/docs/testing/K6_TESTING_SUMMARY.md)**:
   Menyajikan ringkasan eksekutif, gap analysis cakupan modul pengujian, dan *roadmap* implementasi 5 fase.
4. **[Analisis Gap Pengujian (K6_PERFORMANCE_TESTING_ANALYSIS.md)](file:///d:/semester-4-IT%20Del/Semester%20VI/AdminPanel/AdminPanel/docs/testing/K6_PERFORMANCE_TESTING_ANALYSIS.md)**:
   Analisis rinci terhadap pengujian sebelumnya, masalah pencocokan URL, kegagalan autentikasi, serta prioritas pengerjaan berikutnya.
