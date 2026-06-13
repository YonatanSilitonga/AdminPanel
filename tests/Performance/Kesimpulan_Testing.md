# 🎯 Intisari Laporan Performance Testing

## 1. Status Sistem Secara Keseluruhan

- Sistem **layak digunakan**, namun masih terdapat beberapa area yang memerlukan optimasi.
- Sebanyak **7 dari 11 modul** berhasil memenuhi target performa (**63,6% PASS**).
- Sebanyak **4 modul** memiliki waktu respons yang melebihi batas yang ditentukan (**36,4% FAIL**).

---

## 2. Modul yang Memenuhi Target Performa (PASS)

✅ **Dashboard, Events, Users, Destinations, Content Management, Settings, dan Authentication**

Karakteristik:

- Waktu respons rata-rata berada pada rentang **470–990 ms**.
- Seluruh modul merespons dalam waktu kurang dari **1 detik**.
- Tidak ditemukan kesalahan selama pengujian (**0% failure rate**).
- Sistem menunjukkan tingkat stabilitas dan reliabilitas yang baik.

---

## 3. Modul yang Memerlukan Optimasi (FAIL)

| Modul | Permasalahan | Dampak |
|---------|-------------|---------|
| Analytics | Dashboard review summary memiliki waktu respons mencapai 5,65 detik | Pengguna harus menunggu lebih dari 5 detik untuk melihat statistik |
| Reviews | Fitur daftar review dan statistik membutuhkan waktu hingga 3,48 detik | Moderator mengalami keterlambatan saat mengelola review |
| Chatbot Logs | Pagination pada halaman kedua dan seterusnya mencapai 4,92 detik | Sulit mengakses log yang lebih lama |
| Reports / Export | Proses ekspor data membutuhkan waktu hingga 4,81 detik | Administrator tidak dapat melakukan ekspor data secara cepat |

---

## 4. Analisis Penyebab Utama (Root Cause)

### 🔴 Penyebab 1: Query Database Belum Optimal

- Query agregasi pada modul analytics mengakses data dalam jumlah besar tanpa indeks yang memadai.
- Pagination masih menggunakan mekanisme **skip/offset** dengan offset tinggi sehingga memicu proses table scan.

### 🔴 Penyebab 2: Belum Tersedia Mekanisme Caching

- Data yang sering diakses seperti review dan log tidak disimpan dalam cache.
- Setiap permintaan harus mengambil data langsung dari database.

### 🔴 Penyebab 3: Volume Data yang Besar

- Dataset review dan chatbot log terus bertambah.
- Query tidak memiliki pembatasan yang optimal sehingga memproses terlalu banyak data dalam satu waktu.

---

## 5. Rekomendasi Perbaikan

### 1. Menambahkan Database Index

Contoh indeks yang direkomendasikan pada MongoDB:

- `destination_id`
- `status`
- `created_at`

**Dampak yang diharapkan:**

- Waktu respons dapat berkurang hingga **60–70%**.

### 2. Mengimplementasikan Caching

Rekomendasi:

- Cache data analytics selama **5–10 menit**.
- Cache daftar review selama **2–5 menit**.

**Dampak yang diharapkan:**

- Permintaan berulang dapat diproses hampir secara instan.

### 3. Mengoptimalkan Pagination

Rekomendasi:

- Menggunakan **cursor-based pagination**.
- Membatasi jumlah data yang dikirim per halaman (50–100 data).

**Dampak yang diharapkan:**

- Performa halaman berikutnya tetap konsisten seperti halaman pertama.

---

## 6. Estimasi Hasil Setelah Optimasi

### Kondisi Saat Ini

| Modul | Response Time | Status |
|---------|-------------|---------|
| Analytics (Review Summary) | 5,65 detik | ❌ |
| Reviews Index | 3,48 detik | ❌ |
| Chatbot Logs Page 2 | 4,92 detik | ❌ |
| Reports Export | 4,81 detik | ❌ |

### Target Setelah Optimasi

| Modul | Target Response Time | Peningkatan |
|---------|--------------------|-------------|
| Analytics (Review Summary) | 2,0 – 2,5 detik | ±64% lebih cepat |
| Reviews Index | 1,5 – 1,8 detik | ±57% lebih cepat |
| Chatbot Logs Page 2 | 2,0 – 2,3 detik | ±60% lebih cepat |
| Reports Export | 2,5 – 3,0 detik | ±40% lebih cepat |

### Target Akhir

✅ **11 dari 11 modul memenuhi standar performa (100% PASS).**

---

# Kesimpulan

1. Sistem Admin Panel secara keseluruhan menunjukkan performa yang baik dan tidak mengalami error selama pengujian.
2. Ditemukan empat bottleneck utama pada fitur analytics, review management, chatbot logs pagination, dan export data yang menyebabkan waktu respons berada pada rentang 3–5 detik.
3. Implementasi database indexing, caching, dan optimasi pagination diperkirakan mampu meningkatkan performa hingga 60–70% sehingga seluruh modul dapat memenuhi target waktu respons.

---
