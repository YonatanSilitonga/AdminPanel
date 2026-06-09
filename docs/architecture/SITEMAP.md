Sitemap: Admin Panel — Pemetaan dan Rekomendasi
=============================================

Ringkasan
--------
Dokumen ini mencocokkan sitemap yang diinginkan dengan implementasi saat ini di project, mengidentifikasi perbedaan, dan memberikan rekomendasi perbaikan.

Referensi utama implementasi rute: [routes/web.php](routes/web.php#L1-L300)

1) Struktur Sitemap yang Diusulkan (Admin)
----------------------------------------
- Auth
  - Login (/admin/login)
  - Forgot Password (/admin/forgot-password)
  - Reset Password (/admin/reset-password/{token})

- Dashboard
  - Dashboard overview (/admin/dashboard)
  - Chart data (/admin/dashboard/chart-data)
  - Global search (/admin/search)

- Destinations
  - List / Index (/admin/destinations)
  - Create (/admin/destinations/create)
  - Edit (/admin/destinations/{id}/edit)
  - Gallery management (/admin/destinations/{id}/gallery)
  - Facilities (/admin/destinations/{id}/facilities)
  - Trending management (/admin/trending-destinations/*)

- Events
  - List / CRUD (/admin/events)

- Carousel & Banners
  - List / CRUD (/admin/carousel-banners)
  - Sign upload / order / settings endpoints

- Fasilitas Umum (Hotels / Restaurants / SPBU / dsb.)
  - Index / CRUD (/admin/fasilitas-umum)
  - Toggle status (/admin/fasilitas-umum/{id}/toggle-status)

- Budaya
  - Index / CRUD (/admin/budaya)

- Berita & Promosi
  - CRUD (/admin/berita-promosi)

- Reviews
  - Index / Show / Approve / Reject / Export (/admin/reviews)

- Reports
  - Index / Show / Resolve / Export (/admin/reports)

- Users
  - Index / Export / Activity (/admin/users)

- Recommendation Logs, Chatbot Logs, Analytics, Settings, Profile

2) Mapping singkat ke kode
--------------------------
- `destinations` → [DestinationController](app/Http/Controllers/Admin/DestinationController.php)
- `events` → [EventController](app/Http/Controllers/Admin/EventController.php)
- `carousel-banners` → [CarouselBannerController](app/Http/Controllers/Admin/CarouselBannerController.php)
- `fasilitas-umum` (Hotel, Resto, dsb.) → [FasilitasUmumController](app/Http/Controllers/Admin/FasilitasUmumController.php)
- `reviews` → [ReviewController](app/Http/Controllers/Admin/ReviewController.php)
- `reports` → [ReportController](app/Http/Controllers/Admin/ReportController.php)
- Routes lengkap dan middleware ada di [routes/web.php](routes/web.php#L1-L300)

3) Perbedaan / Mismatch yang Ditemukan
------------------------------------
- Sitemap (visual) menampilkan node terpisah untuk `Restaurants` dan `Hotels`.
  - Implementasi saat ini menangani keduanya lewat `fasilitas-umum` dan properti `type` (lihat controller).
  - Konsekuensi: jika tim atau desain mengharapkan halaman admin khusus untuk "Restaurants" atau "Hotels", perlu dibuat resource/route baru.

- Sitemap menyebut "Categories" sebagai entitas terpisah.
  - Tidak ditemukan `CategoryController` atau resource `categories` di implementasi.
  - Jika kategori adalah entitas yang dikelola, tambahkan resource `categories` atau representasikan kategori sebagai model/field pada `destinations`.

- Beberapa halaman granular (mis. "Manage Menus", "Set Hotel Detail") tidak muncul sebagai rute eksplisit.
  - Mungkin diimplementasikan sebagai modal/partial view dalam `Destination` atau `FasilitasUmum`. Jika dibutuhkan rute terpisah, tambahkan endpoints dan views.

4) Rekomendasi Perbaikan (dua opsi)
----------------------------------
- Opsi A — Sinkronisasi sitemap ke implementasi (cepat, dokumentasi):
  - Perbarui sitemap visual/dokumentasi untuk menjelaskan bahwa Restaurants/Hotels dikelola lewat `Fasilitas Umum`.
  - Tambahkan keterangan bahwa "Categories" saat ini bukan resource terpisah.
  - Keuntungan: cepat, tidak ubah kode.

- Opsi B — Ubah implementasi agar sesuai sitemap (lebih kerja):
  - Tambah resource `hotels` dan `restaurants` dengan controller, model (jika diperlukan), views, dan routes.
  - Tambah resource `categories` untuk CRUD kategori.
  - Perbarui permission/middleware sesuai desain sitemap.
  - Keuntungan: sitemap dan UI admin jadi match, lebih jelas untuk content editors.

5) Rekomendasi Struktur Routes (contoh bila memilih Opsi B)
---------------------------------------------------------
- `Route::resource('hotels', HotelController::class);`
- `Route::resource('restaurants', RestaurantController::class);`
- `Route::resource('categories', CategoryController::class);`

6) Panduan singkat membuat sitemap yang baik (best practices)
-----------------------------------------------------------
- **Hierarchy jelas**: struktur pohon yang mencerminkan navigasi dan izin (Admin vs Public).
- **URL ≈ Resource**: gunakan resource-based routes (RESTful) untuk CRUD.
- **Satu sumber kebenaran**: sitemap harus cocok dengan `routes/web.php` dan dokumentasi.
- **Tingkat abstraksi**: jangan pecah entitas kecil menjadi halaman terpisah kecuali perlu (contoh: "Manage Menu" bisa jadi bagian dari destination jika tidak berdiri sendiri).
- **Masukkan info permission**: catat role yang diperlukan untuk tiap node (admin, moderator, super_admin).
- **Tanda status**: tandai node apakah sudah diimplementasikan, atau perlu desain / backend / view.

7) Langkah berikutnya yang saya bisa lakukan
-----------------------------------------
- Jika pilih Opsi A: Saya akan memperbarui diagram/sitemap (file ini) dan `docs/` lainnya.
- Jika pilih Opsi B: Saya bisa mulai membuat rute dan scaffold controller + view stub untuk `hotels`/`restaurants`/`categories`.

---
File ini dibuat untuk memudahkan keputusan. Pilih Opsi A (sinkronisasi dokumentasi) atau Opsi B (ubah kode). Saya bisa langsung implementasikan setelah Anda pilih.
