# 🖥️ Laravel Admin Panel - Smart Tourism

File panduan ini menjelaskan langkah setup, deskripsi folder, dan deskripsi proyek untuk **Laravel Admin Panel**. 

> 💡 **Klarifikasi Bahasa Pemrograman**: Proyek ini **100% menggunakan PHP & Laravel Framework 12.x** (bukan menggunakan bahasa pemrograman Go/Golang). Istilah "Go" pada nama platform (seperti Smart Tourism Go) hanyalah penamaan/brand aplikasi client, sedangkan sistem Admin Panel ini sepenuhnya adalah aplikasi Laravel.

---

## 📝 Deskripsi Proyek

**Laravel Admin Panel** adalah sistem manajemen konten (CMS) berbasis web yang digunakan oleh administrator untuk mengelola seluruh data platform pariwisata. Proyek ini menggunakan **MongoDB** sebagai database utama untuk fleksibilitas penyimpanan dokumen pariwisata dan **Cloudinary API** untuk penyimpanan gambar secara dinamis di cloud.

Sistem ini memiliki fitur utama untuk mengelola:
*   **Destinasi Wisata**: CRUD data destinasi, rating, harga tiket, lokasi koordinat, dan galeri foto.
*   **Ulasan & Analitik**: Memantau ulasan dari pengunjung yang diklasifikasikan menggunakan API Sentiment Analysis.
*   **Konten Informasi**: Event pariwisata, budaya lokal, berita/promosi, serta fasilitas umum.
*   **Log & Monitoring**: Audit log aktivitas admin, riwayat percakapan chatbot AI, dan log rekomendasi destinasi.

---

## 📁 Deskripsi Folder Proyek (Laravel Structure)

Berikut adalah fungsi dari folder-folder utama di dalam proyek Laravel Admin Panel ini:

*   📂 **`app/`**: Pusat logika aplikasi.
    *   `Http/Controllers/Admin/`: Berisi file controller untuk menangani logika request (seperti `ReviewController`, `DestinationController`, `DashboardController`, dll.).
    *   `Http/Requests/Admin/`: Form request validation untuk memvalidasi input admin (seperti `DestinationRequest`).
    *   `Services/`: Berisi class layanan seperti `SentimentAnalysisService` untuk berkomunikasi dengan model analisis sentimen.
*   📂 **`bootstrap/`**: Berisi file bootstrap framework dan file konfigurasi cache untuk performa aplikasi.
*   📂 **`config/`**: Berisi seluruh file konfigurasi sistem (database, mail, session, cloudinary, serta file `sentiment.php` untuk integrasi model).
*   📂 **`database/`**: Berisi file migrasi (`migrations/`) untuk membuat koleksi dan indeks di MongoDB, serta `factories/` dan `seeders/` untuk data dummy.
*   📂 **`public/`**: Folder publik yang dapat diakses oleh web server. Berisi file `index.php` dan aset terkompilasi (CSS/JS).
*   📂 **`resources/`**: Berisi aset mentah.
    *   `views/admin/`: File template tampilan UI berbasis Blade PHP (seperti `destinations/edit.blade.php`, `reviews/index.blade.php`, dll.).
    *   `css/` & `js/`: File stylesheet dan skrip Javascript mentah sebelum dicompile oleh Vite.
*   📂 **`routes/`**: Berisi file rute aplikasi. Rute dashboard admin dikelompokkan di dalam `routes/admin.php` dan `routes/web.php`.
*   📂 **`storage/`**: Digunakan untuk menyimpan log aplikasi (`storage/logs/laravel.log`) dan cache file.

---

## ⚙️ Persyaratan Sistem (Prerequisites)

Sebelum menjalankan aplikasi, pastikan komputer Anda telah terinstal:
1.  **PHP** >= 8.2 (dilengkapi ekstensi `mongodb`)
2.  **Composer** (Manajer paket PHP)
3.  **MongoDB Server** (Lokal) atau akun **MongoDB Atlas** (Cloud)

---

## 🚀 Panduan Menjalankan Pertama Kali (First Run Setup)

Setelah mengekstrak file zip `AdminPanel`, jalankan perintah-perintah berikut secara berurutan:

### Langkah 1: Masuk ke Folder Proyek
Buka terminal/command prompt Anda, lalu masuk ke folder hasil ekstrak:
```bash
cd AdminPanel
```

### Langkah 2: Install Dependensi PHP
Unduh semua library PHP yang dibutuhkan proyek dengan Composer:
```bash
composer install
```

### Langkah 3: Setup Environment File (`.env`)
Salin file konfigurasi lingkungan dari template default:
```bash
# Linux / macOS / PowerShell:
cp .env.example .env

# Windows CMD:
copy .env.example .env
```
Buka file `.env` yang baru disalin menggunakan text editor, kemudian sesuaikan parameter koneksi database Anda.

#### 🔹 Pilihan A: Menggunakan MongoDB Atlas (Rekomendasi Hosting/Cloud)
Jika Anda menggunakan MongoDB Atlas, gunakan format URI `mongodb+srv`. Pastikan Anda membungkus nilai `MONGODB_URI` dengan tanda kutip ganda (`""`) untuk menghindari error pembacaan karakter khusus (seperti `@` atau `/` pada password):

```env
DB_CONNECTION=mongodb
MONGODB_URI="mongodb+srv://<username_atlas>:<password_atlas>@<cluster-address>.mongodb.net/?retryWrites=true&w=majority"
MONGODB_DATABASE=smart_tourism
```

> ⚠️ **Catatan Penting MongoDB Atlas**:
> 1. Pastikan Anda telah mengonfigurasi **Network Access IP Whitelist** di panel MongoDB Atlas Anda (masukkan `0.0.0.0/0` untuk memperbolehkan koneksi dari mana saja saat masa pengembangan/hosting).
> 2. Pastikan database user yang Anda buat memiliki hak akses membaca dan menulis database (`readWriteAnyDatabase` atau `dbAdmin`).

#### 🔹 Pilihan B: Menggunakan MongoDB Lokal
Jika menggunakan server lokal:
```env
DB_CONNECTION=mongodb
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=smart_tourism
```

#### 🔹 Konfigurasi Tambahan di `.env`:
```env
# Koneksi Cloudinary (Media Gambar)
CLOUDINARY_URL=cloudinary://<API_KEY>:<API_SECRET>@<CLOUD_NAME>

# Konfigurasi Koneksi Sentiment Service (Python API)
SENTIMENT_SERVICE_ENABLED=true
SENTIMENT_API_URL=http://127.0.0.1:5000
SENTIMENT_API_TIMEOUT=30
```

### Langkah 4: Generate Application Key
Jalankan perintah ini untuk membuat kunci enkripsi unik Laravel:
```bash
php artisan key:generate
```

### Langkah 5: Jalankan Migrasi Database
Buat koleksi (tabel) dan indeks awal di MongoDB (Lokal / Atlas):
```bash
php artisan migrate
```

### Langkah 6: Jalankan Server
Jalankan web server lokal Laravel:
```bash
php artisan serve
```
Aplikasi Admin Panel Anda sekarang dapat diakses secara lokal di browser melalui alamat: **`http://127.0.0.1:8000`**
