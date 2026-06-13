# 🧹 Panduan Pembersihan Data Dummy

## 📋 Ringkasan

Command `admin:clean-dummy` telah diperbarui untuk mendeteksi dan menghapus data dummy dari database MongoDB dengan aman, tanpa menghapus data asli.

## ✅ Hasil Pembersihan

### Data Dummy yang Berhasil Dihapus

| Collection | Data Dihapus | Pola yang Terdeteksi |
|------------|--------------|----------------------|
| **Users** | 53 users | Semua email `@example.com` |
| **Events** | Sudah bersih | `Test Event {n}`, `Event Test {n}` |
| **Fasilitas** | Sudah bersih | `Fasilitas {n}`, `Jl. Test {n}` |
| **Admins** | Sudah bersih | Factory dummy (bcrypt cost 04) |
| **Destinations** | Sudah bersih | Placeholder images, test names |
| **Budaya** | Sudah bersih | Placeholder images, test names |

### 📊 Data Asli Tersisa

```
✅ Users      : 25 (data asli)
✅ Events     : 29 (data asli)
✅ Fasilitas  : 29 (data asli)
✅ Admins     : 1  (Super Admin)
```

## 🎯 Pola Deteksi Data Dummy

### 1. Users Collection
- **Email dengan domain `@example.com`**
  - `test@example.com`
  - `testuser{n}@example.com`
  - `api_login_{timestamp}@example.com`
  - Semua email yang menggunakan domain `@example.com`
- **Nama Test User**
  - `firstName = "Test"` AND `lastName = "User"`

> **Catatan**: Domain `example.com` adalah domain reserved untuk testing sesuai RFC 2606

### 2. Events Collection
- **Nama dengan pola test**:
  - `Event Test {n}`
  - `Test Event {n}`
  - `Event {n} at {location}`
- **Slug dengan pola test**:
  - `test-event-{n}-{timestamp}`
- **Deskripsi dengan pola**:
  - `Event {n}: Lorem ipsum...`
- **Soft-deleted events** dengan pola nama di atas

### 3. Fasilitas Umum Collection
- **Nama dengan pola**:
  - `Fasilitas {n}` (angka saja, contoh: "Fasilitas 24")
  - `Fasilitas Umum Test {n}`
- **Alamat dengan pola test**:
  - `Jl. Test {n}, Danau Toba`
- **Soft-deleted fasilitas** dengan pola di atas

### 4. Destinations Collection
- **Gambar placeholder**:
  - URL mengandung: `via.placeholder.com`, `placehold.co`, `picsum.photos`, dll
- **Nama dengan pola**:
  - `Destination Test {n}`
  - `Destination {n}`

### 5. Budaya Collection
- **Gambar placeholder** (sama seperti Destinations)
- **Nama dengan pola**:
  - `Budaya Test {n}`

### 6. Admins Collection
⚠️ **PENTING**: Admin asli `superadmin@smarttourism.local` **DIJAGA** agar tidak terhapus

**Pola admin dummy**:
- Password bcrypt dengan cost 04: `$2y$04$...` atau `$2a$04$...`
- Email dengan pola: `testadmin*` atau `dummyadmin*`
- **DIKECUALIKAN**: `superadmin@smarttourism.local` (admin asli dari AdminSeeder)

## 🚀 Cara Penggunaan

### 1. Preview (Dry Run) - Aman
Lihat data yang akan dihapus tanpa benar-benar menghapusnya:

```bash
# Preview semua collection
php artisan admin:clean-dummy --dry-run

# Preview collection tertentu
php artisan admin:clean-dummy --dry-run --collection=users
php artisan admin:clean-dummy --dry-run --collection=events
php artisan admin:clean-dummy --dry-run --collection=fasilitas_umum
```

### 2. Hapus Data Dummy

```bash
# Hapus semua data dummy
php artisan admin:clean-dummy

# Hapus data dummy dari collection tertentu
php artisan admin:clean-dummy --collection=users
php artisan admin:clean-dummy --collection=events
php artisan admin:clean-dummy --collection=fasilitas_umum
php artisan admin:clean-dummy --collection=destinations
php artisan admin:clean-dummy --collection=budaya
php artisan admin:clean-dummy --collection=admins
```

### 3. Verifikasi Hasil

Setelah menjalankan command, verifikasi dengan:

```bash
php artisan admin:clean-dummy --dry-run
```

Jika output menunjukkan `Total ditemukan: 0 data dummy`, berarti database sudah bersih! ✅

## 🛡️ Keamanan & Proteksi

### Data Yang Dijaga (Tidak Akan Terhapus)
1. ✅ Admin asli: `superadmin@smarttourism.local`
2. ✅ Users dengan email domain selain `@example.com`
3. ✅ Events tanpa pola nama test
4. ✅ Fasilitas dengan nama dan alamat asli
5. ✅ Semua data dengan pola nama yang tidak cocok dengan dummy

### Konfirmasi Keamanan
- Command akan menanyakan konfirmasi sebelum menghapus (kecuali mode dry-run)
- Preview data yang akan dihapus sebelum eksekusi
- Tidak ada penghapusan otomatis tanpa konfirmasi pengguna

## 📝 Contoh Output

### Dry Run (Preview)
```
🧹 Clean Dummy Data  [DRY-RUN — tidak ada yang dihapus]

👤 users
  Ditemukan: 53 data dummy
  → 69e9d593dba59c4b7c0c56a6  "Antonia Ziemann"  email=amya73@example.com
  → 69f2f860d397261497d3ac25  "Login Test User"  email=api_login_1777534632687@example.com
  → ... dan 50 lainnya

─────────────────────────────────────────────
DRY-RUN — Total ditemukan: 53 data dummy
```

### Eksekusi Hapus
```
🧹 Clean Dummy Data  [MODE HAPUS AKTIF]

⚠️  Data yang dihapus TIDAK BISA dikembalikan. Lanjutkan? (yes/no) [no]:
> yes

👤 users
  Ditemukan: 53 data dummy
  ✓ Dihapus: 53

─────────────────────────────────────────────
✅ Total dihapus: 53 data dummy
```

## 🔧 Troubleshooting

### Jika Admin Asli Ikut Terhapus
Jalankan seeder untuk memulihkan:
```bash
php artisan db:seed --class=AdminSeeder
```

Kredensial admin yang dipulihkan:
- Email: `superadmin@smarttourism.local`
- Password: `SuperAdmin@123`

### Jika Masih Ada Data Dummy Tersisa
Periksa pola data yang tidak terdeteksi dan update command di:
```
app/Console/Commands/CleanDummyData.php
```

## 📅 Riwayat Update

### Update Terakhir (Juni 2026)
- ✅ Menambahkan deteksi untuk semua email `@example.com`
- ✅ Menambahkan proteksi untuk admin asli `superadmin@smarttourism.local`
- ✅ Menambahkan deteksi slug pattern untuk events
- ✅ Menambahkan deteksi soft-deleted dummy data
- ✅ Berhasil membersihkan 53 user dummy dengan email `@example.com`

## 🎉 Kesimpulan

Database telah **berhasil dibersihkan** dari data dummy!

**Data Dummy Dihapus:**
- ✅ 53 users dengan `@example.com`
- ✅ test@example.com
- ✅ api_login_*@example.com
- ✅ Test Event 7 (sudah bersih sebelumnya)
- ✅ Fasilitas 24 (sudah bersih sebelumnya)

**Data Asli Aman:**
- ✅ 25 users asli
- ✅ 29 events asli
- ✅ 29 fasilitas asli
- ✅ 1 admin asli (Super Admin)

Database siap untuk produksi! 🚀
