# Dokumentasi Menu Settings - Admin Panel

## 📋 Daftar Menu Settings yang Telah Dibuat

Semua menu settings telah berhasil diimplementasikan dengan design yang konsisten. Berikut adalah daftar lengkapnya:

### 1. ✅ **Profil Saya** (Sudah Ada)
- Route: `admin.profile`
- Fungsi: Pengaturan profil admin pribadi

### 2. ✅ **Pengaturan Umum** (Sudah Ada - Updated)
- Route: `admin.settings.general`
- Fungsi: Identitas aplikasi, konten, dan fitur platform
- Fields:
  - Nama Aplikasi
  - Email Dukungan
  - Deskripsi Singkat
  - Nomor Kontak
  - Zona Waktu
  - Item Per Halaman
  - Max Upload Size
  - Format Tanggal
  - Toggle: Sistem Ulasan, Sistem Laporan, Auto-Approve Ulasan
  - Mode Pemeliharaan

### 3. 🆕 **Email & Notifikasi** (BARU)
- Route: `admin.settings.email-notifications`
- Fungsi: Konfigurasi SMTP dan notifikasi sistem
- Fields:
  - SMTP Host
  - SMTP Port
  - SMTP Username
  - SMTP Password
  - SMTP Encryption (TLS/SSL/None)
  - Email Pengirim
  - Nama Pengirim
  - Toggle: Notifikasi Ulasan Baru, Laporan Baru, Pengguna Baru, Error Sistem

### 4. 🆕 **Tampilan** (BARU)
- Route: `admin.settings.appearance`
- Fungsi: Kustomisasi logo, warna, dan bahasa
- Fields:
  - Upload Logo
  - Upload Favicon
  - Warna Utama (Color Picker)
  - Warna Sekunder (Color Picker)
  - Bahasa Default (Indonesia/English)
  - Toggle: Mode Gelap

### 5. 🆕 **Keamanan** (BARU)
- Route: `admin.settings.security`
- Fungsi: Pengaturan keamanan dan kebijakan password
- Fields:
  - Session Timeout (5-1440 menit)
  - Auto Logout (5-120 menit)
  - Panjang Minimal Password (6-20 karakter)
  - Toggle: Wajib Angka dalam Password
  - Toggle: Wajib Simbol dalam Password
  - Maksimal Percobaan Login (3-10 kali)
  - Toggle: Two-Factor Authentication (2FA)

### 6. 🆕 **Media & Upload** (BARU)
- Route: `admin.settings.media`
- Fungsi: Konfigurasi file upload dan pemrosesan gambar
- Fields:
  - Tipe File Diizinkan (Checkbox: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx)
  - Ukuran Maksimal File (1-50 MB)
  - Kualitas Gambar (60-100%)
  - Lebar Thumbnail (50-500 px)
  - Tinggi Thumbnail (50-500 px)
  - Toggle: Generate Thumbnail Otomatis

### 7. 🆕 **SEO** (BARU)
- Route: `admin.settings.seo`
- Fungsi: Optimasi mesin pencari dan tracking
- Fields:
  - Meta Title (max 60 karakter)
  - Meta Description (max 160 karakter)
  - Meta Keywords
  - Google Analytics ID
  - Facebook Pixel ID
  - Toggle: Auto Generate Sitemap

### 8. 🆕 **Moderasi** (BARU)
- Route: `admin.settings.moderation`
- Fungsi: Pengaturan moderasi konten dan filter spam
- Fields:
  - Toggle: Moderasi Ulasan
  - Toggle: Moderasi Komentar
  - Kata Terlarang (Textarea, satu per baris)
  - Hapus Spam Otomatis Setelah (1-365 hari)
  - Rating Minimal untuk Auto-Approve (1-5 bintang)

### 9. 🆕 **Sistem** (BARU)
- Route: `admin.settings.system`
- Fungsi: Konfigurasi cache, logging, dan maintenance
- Fields:
  - Toggle: Aktifkan Cache
  - Durasi Cache (1-1440 menit)
  - Level Logging (error/warning/info/debug)
  - Toggle: Mode Debug (⚠️ Development only)
  - Button: Bersihkan Cache
  - Button: Optimasi Database

### 10. ✅ **Log Audit** (Sudah Ada)
- Route: `admin.settings.audit-logs`
- Fungsi: Riwayat aktivitas admin

---

## 🗂️ Struktur File yang Dibuat

### Controllers
```
app/Http/Controllers/Admin/SettingsController.php
```
**Methods Baru:**
- `editEmailNotifications()` & `updateEmailNotifications()`
- `editAppearance()` & `updateAppearance()`
- `editSecurity()` & `updateSecurity()`
- `editMedia()` & `updateMedia()`
- `editSeo()` & `updateSeo()`
- `editModeration()` & `updateModeration()`
- `editSystem()` & `updateSystem()`
- `clearCache()`
- `optimizeDatabase()`

### Routes
```
routes/web.php
```
**Routes Baru:**
- GET/PUT `admin/settings/email-notifications`
- GET/POST `admin/settings/appearance`
- GET/PUT `admin/settings/security`
- GET/PUT `admin/settings/media`
- GET/PUT `admin/settings/seo`
- GET/PUT `admin/settings/moderation`
- GET/PUT `admin/settings/system`
- POST `admin/settings/clear-cache`
- POST `admin/settings/optimize-database`

### Views
```
resources/views/admin/settings/
├── partials/
│   └── tabs.blade.php (Navigation tabs untuk semua halaman settings)
├── general.blade.php (Updated)
├── email-notifications.blade.php (BARU)
├── appearance.blade.php (BARU)
├── security.blade.php (BARU)
├── media.blade.php (BARU)
├── seo.blade.php (BARU)
├── moderation.blade.php (BARU)
└── system.blade.php (BARU)
```

---

## 🎨 Design Consistency

Semua halaman menggunakan design pattern yang sama:

### 1. **Layout Structure**
- Breadcrumb navigation
- Tabs navigation (menggunakan partial)
- Form dengan sections
- Submit buttons di bawah

### 2. **Section Cards**
```html
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    <!-- Header dengan icon dan deskripsi -->
    <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30">
        <!-- Icon + Title + Description -->
    </div>
    
    <!-- Content -->
    <div class="p-8 space-y-6">
        <!-- Form fields -->
    </div>
</div>
```

### 3. **Form Elements**
- Input fields: `rounded-xl` dengan `focus:ring-2`
- Toggles: Custom switch dengan `peer` classes
- Buttons: `rounded-2xl` dengan hover effects
- Color scheme: Konsisten dengan sidebar color

### 4. **Icons**
- Setiap section memiliki icon yang relevan
- Icon colors: sidebar (primary), blue, emerald, orange
- Consistent sizing: `w-5 h-5` untuk icons

---

## 🔧 Cara Menggunakan

### 1. Akses Menu Settings
```
URL: http://your-domain/admin/settings/general
```

### 2. Navigasi Antar Menu
Gunakan tabs di bagian atas untuk berpindah antar menu settings.

### 3. Simpan Perubahan
Klik tombol "Simpan Perubahan" di bagian bawah form.

### 4. Validasi
Semua field required akan divalidasi sebelum disimpan.

---

## 📊 Database Storage

Semua settings disimpan di MongoDB collection `app_settings` dengan struktur:
```json
{
  "_id": "ObjectId",
  "key": "setting_name",
  "value": "setting_value",
  "type": "string|integer|float|boolean|json",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Contoh Penggunaan di Code:
```php
// Get setting
$value = AppSetting::get('smtp_host', 'default_value');

// Set setting
AppSetting::set('smtp_host', 'smtp.gmail.com');

// Set dengan tipe data
AppSetting::set('max_upload_size', 10, 'integer');
AppSetting::set('enable_cache', true, 'boolean');
```

---

## ✅ Fitur Keamanan

1. **Middleware Protection**: Semua routes dilindungi dengan `admin.role:super_admin`
2. **CSRF Protection**: Semua form menggunakan `@csrf` token
3. **Input Validation**: Validasi di controller untuk semua input
4. **Audit Logging**: Semua perubahan dicatat di audit log
5. **Password Fields**: Input password menggunakan type password dengan toggle visibility

---

## 🚀 Testing

### Manual Testing Checklist:
- [ ] Akses setiap menu settings
- [ ] Submit form dengan data valid
- [ ] Submit form dengan data invalid (test validasi)
- [ ] Test toggle switches
- [ ] Test file uploads (logo, favicon)
- [ ] Test color pickers
- [ ] Test range sliders
- [ ] Test clear cache button
- [ ] Test optimize database button
- [ ] Verify data tersimpan di database
- [ ] Verify audit log tercatat

---

## 📝 Notes

1. **Menu API & Integrasi** dan **Konfigurasi AI** masih ada untuk backward compatibility
2. Semua menu baru menggunakan design yang sama dengan menu yang sudah ada
3. Tabs navigation menggunakan partial untuk kemudahan maintenance
4. Semua settings disimpan di database, bukan di file config
5. Support untuk multiple languages (ID/EN) sudah disiapkan

---

## 🎯 Next Steps (Opsional)

Jika ingin pengembangan lebih lanjut:

1. **Backup & Restore**: Implementasi fitur backup/restore database
2. **Email Testing**: Tambahkan button "Test Email" di Email & Notifications
3. **Import/Export Settings**: Export/import settings sebagai JSON
4. **Settings History**: Riwayat perubahan settings dengan rollback
5. **Multi-language Support**: Implementasi penuh untuk bahasa Indonesia dan English

---

## 👨‍💻 Developer Info

**Created by**: Kiro AI Assistant
**Date**: 2026-05-20
**Version**: 1.0.0
**Framework**: Laravel 10 + MongoDB
**Design**: Tailwind CSS + Alpine.js

---

## 📞 Support

Jika ada pertanyaan atau issue, silakan hubungi tim development.
