# Laporan Test Case Mega-Komprehensif (Sesuai Sitemap Proyek)

**Proyek:** Toba Tourism Admin Panel  
**Dokumentasi:** Berdasarkan Struktur Sitemap Proyek  
**Status Dokumentasi:** Final (Mencakup seluruh fitur yang dikembangkan)  
**Update Terakhir:** Verifikasi lengkap terhadap routes dan controllers  
**Total Test Cases:** 124

---

## I. MODUL MANAJEMEN KONTEN (ORANYE)

### 1. Dashboard & Analytics
- **TC-DASH-01:** Verifikasi tampilan dashboard utama dengan ringkasan statistik.
- **TC-DASH-02:** Verifikasi tampilan grafik data analitik kunjungan real-time.
- **TC-DASH-03:** Verifikasi pembaruan data pada kartu ringkasan (Summary Cards) otomatis.
- **TC-DASH-04:** Pengujian endpoint chart-data API untuk visualisasi grafik.

### 2. Pencarian Global (Global Search)
- **TC-SRCH-01:** Pengujian fitur pencarian global di seluruh modul konten.
- **TC-SRCH-02:** Verifikasi hasil pencarian menampilkan destinasi, event, budaya, dan berita.
- **TC-SRCH-03:** Pengujian filter pencarian berdasarkan kategori konten.

### 3. Destinasi Wisata (Full CRUD & Media)
- **TC-DEST-01:** Pengujian **Index**: Tampilkan daftar destinasi dengan pagination, pencarian, dan filter.
- **TC-DEST-02:** Pengujian **Create**: Validasi form input destinasi baru (nama, deskripsi, lokasi, kategori, rating).
- **TC-DEST-03:** Pengujian **Store**: Penyimpanan data destinasi baru ke database.
- **TC-DEST-04:** Pengujian **Edit**: Tampilkan form edit destinasi dengan data yang sudah ada.
- **TC-DEST-05:** Pengujian **Update**: Perubahan data deskripsi, lokasi, kategori, koordinat, dan detail destinasi.
- **TC-DEST-06:** Pengujian **Delete/Destroy**: Menghapus destinasi dari sistem.
- **TC-DEST-07:** Pengujian **Toggle Featured**: Menjadikan destinasi sebagai 'Unggulan' (featured).
- **TC-DEST-08:** Pengujian **Toggle Status**: Mengaktifkan/menonaktifkan visibilitas destinasi di aplikasi mobile.
- **TC-DEST-09:** **Gallery Management - Store**: Menambahkan foto baru ke galeri destinasi.
- **TC-DEST-10:** **Gallery Management - Delete**: Menghapus foto dari galeri destinasi.
- **TC-DEST-11:** **Gallery Management - Order**: Mengubah urutan (sorting) foto dalam galeri destinasi.
- **TC-DEST-12:** **Facilities Management - Store**: Menambahkan fasilitas (ATM, Toilet, Parkir, dll) ke destinasi.
- **TC-DEST-13:** **Facilities Management - Delete**: Menghapus fasilitas dari destinasi.

### 4. Destinasi Trending (Featured)
- **TC-TRND-01:** Pengujian tampilan dashboard Trending Destinations.
- **TC-TRND-02:** Pengujian **Update Mode**: Berpindah antara mode Otomatis (berdasarkan rating/reviews) dan Manual.
- **TC-TRND-03:** Pengujian **Search Destinations**: Pencarian destinasi untuk ditambahkan ke trending.
- **TC-TRND-04:** Pengujian **Add Destination**: Menambahkan destinasi ke daftar trending manual.
- **TC-TRND-05:** Pengujian **Remove Destination**: Menghapus destinasi dari trending manual.
- **TC-TRND-06:** Pengujian **Update Order**: Mengatur urutan destinasi trending untuk display.
- **TC-TRND-07:** Pengujian **Reset to Automatic**: Mengembalikan pengaturan trending ke mode otomatis default.

### 5. Event (CRUD & Status Management)
- **TC-EVNT-01:** Pengujian **Index**: Tampilkan daftar event dengan pagination dan filter.
- **TC-EVNT-02:** Pengujian **Create**: Validasi form input event baru.
- **TC-EVNT-03:** Pengujian **Store**: Penyimpanan data event baru.
- **TC-EVNT-04:** Pengujian **Edit**: Tampilkan form edit event dengan data existing.
- **TC-EVNT-05:** Pengujian **Update**: Perubahan data event (nama, deskripsi, tanggal, lokasi, kategori).
- **TC-EVNT-06:** Pengujian **Delete**: Menghapus event dari sistem.
- **TC-EVNT-07:** Pengujian **Toggle Status**: Mengubah status event (aktif/selesai).

### 6. Carousel & Banner (Media & Display)
- **TC-CRSL-01:** Pengujian **Index**: Tampilkan daftar carousel/banner slides dengan status.
- **TC-CRSL-02:** Pengujian **Create**: Validasi form input slide baru.
- **TC-CRSL-03:** Pengujian **Store**: Penyimpanan slide baru ke carousel dengan upload gambar.
- **TC-CRSL-04:** Pengujian **Edit**: Tampilkan form edit carousel slide.
- **TC-CRSL-05:** Pengujian **Update**: Perubahan data slide (judul, deskripsi, gambar, link destination).
- **TC-CRSL-06:** Pengujian **Delete**: Menghapus slide dari carousel.
- **TC-CRSL-07:** Pengujian **Toggle Active**: Mengaktifkan/menonaktifkan slide di aplikasi.
- **TC-CRSL-08:** Pengujian **Reorder**: Mengatur ulang urutan slide untuk tampilan mobile dan web.

### 7. Fasilitas Umum (Global/Peta Wisata)
- **TC-FAS-01:** Pengujian **Index**: Tampilkan daftar fasilitas umum dengan filter dan pagination.
- **TC-FAS-02:** Pengujian **Store**: Menambahkan fasilitas umum baru (tipe, nama, lokasi, deskripsi).
- **TC-FAS-03:** Pengujian **Edit**: Tampilkan form edit fasilitas umum dengan data existing.
- **TC-FAS-04:** Pengujian **Update**: Perubahan data fasilitas (nama, deskripsi, koordinat, tipe, status).
- **TC-FAS-05:** Pengujian **Delete**: Menghapus fasilitas umum dari sistem.

### 8. Budaya & Heritage (CRUD & Status Management)
- **TC-CULT-01:** Pengujian **Index**: Tampilkan daftar budaya/heritage dengan pagination dan filter.
- **TC-CULT-02:** Pengujian **Store**: Menambahkan budaya baru ke sistem dengan detail lengkap.
- **TC-CULT-03:** Pengujian **Edit**: Tampilkan form edit budaya dengan data existing.
- **TC-CULT-04:** Pengujian **Update**: Perubahan data budaya (nama, deskripsi, sejarah, kategori, asal-usul).
- **TC-CULT-05:** Pengujian **Delete**: Menghapus budaya dari sistem.
- **TC-CULT-06:** Pengujian **Toggle Status**: Mengaktifkan/menonaktifkan budaya di tampilan aplikasi.

### 9. Berita & Promosi (CRUD & Publishing)
- **TC-NEWS-01:** Pengujian **Index**: Tampilkan daftar berita/promosi dengan pagination dan filter.
- **TC-NEWS-02:** Pengujian **Store**: Menambahkan berita/promosi baru dengan judul, konten, kategori.
- **TC-NEWS-03:** Pengujian **Edit**: Tampilkan form edit berita dengan data existing.
- **TC-NEWS-04:** Pengujian **Update**: Perubahan data berita (judul, konten, gambar, kategori, tanggal publikasi).
- **TC-NEWS-05:** Pengujian **Delete**: Menghapus berita dari sistem.

---

## II. MODUL ADMINISTRASI & MONITORING (PINK)

### 1. Manajemen Pengguna (Users)
- **TC-USER-01:** Pengujian **Index**: Tampilkan daftar user aplikasi mobile dengan pagination dan pencarian.
- **TC-USER-02:** Pengujian **Store**: Menambahkan user baru ke sistem.
- **TC-USER-03:** Pengujian **Edit**: Tampilkan form edit data user dengan informasi lengkap.
- **TC-USER-04:** Pengujian **Update**: Perubahan data user (nama, email, profile, preferensi).
- **TC-USER-05:** Pengujian **Delete**: Menghapus user dari sistem.
- **TC-USER-06:** Pengujian **Toggle Status**: Blokir/aktifkan akses pengguna ke aplikasi mobile.
- **TC-USER-07:** Pengujian **Activity Tracking**: Melihat riwayat aktivitas user (login, pencarian, wishlist, review).
- **TC-USER-08:** Pengujian **Activity Detail**: Melihat detail lengkap setiap aksi user.

### 2. Monitoring Ulasan (Reviews) & Sentiment Analysis
- **TC-REV-01:** Pengujian **Index**: Tampilkan daftar ulasan dengan pagination, filter, dan status.
- **TC-REV-02:** Pengujian **Show Detail**: Melihat detail ulasan lengkap (user, destinasi, rating, komentar).
- **TC-REV-03:** Pengujian **Analyze Sentiment**: Menjalankan analisis sentimen AI untuk satu ulasan.
- **TC-REV-04:** Pengujian **Analyze Batch**: Menjalankan analisis sentimen AI untuk multiple ulasan sekaligus.
- **TC-REV-05:** Pengujian **Approve**: Menyetujui ulasan untuk ditampilkan di aplikasi mobile.
- **TC-REV-06:** Pengujian **Reject**: Menolak ulasan agar tidak ditampilkan.
- **TC-REV-07:** Pengujian **Delete**: Menghapus ulasan dari sistem sepenuhnya.

### 3. Moderasi Laporan (Reports & Content Moderation)
- **TC-REPT-01:** Pengujian **Index**: Tampilkan daftar laporan/report dengan pagination dan filter.
- **TC-REPT-02:** Pengujian **Show Detail**: Melihat detail laporan (tipe, konten yang dilaporkan, alasan, reporter).
- **TC-REPT-03:** Pengujian **Resolve**: Menandai laporan sebagai selesai/ditangani.
- **TC-REPT-04:** Pengujian **Take Action**: Memberikan aksi resolusi pada konten yang dilaporkan (hapus, warning, dll).
- **TC-REPT-05:** Pengujian **Flag**: Menandai laporan sebagai urgent/penting untuk review lebih lanjut.
- **TC-REPT-06:** Pengujian **Delete**: Menghapus laporan dari sistem.

### 4. Monitoring AI Features (Chatbot & Recommendations)
- **TC-AILOG-01:** Pengujian **Chatbot Logs - Index**: Tampilkan daftar chatbot logs dengan filter tanggal dan user.
- **TC-AILOG-02:** Pengujian **Chatbot Logs - Show**: Melihat detail percakapan chatbot lengkap dengan respons AI.
- **TC-AILOG-03:** Pengujian **Chatbot Logs - Flag**: Menandai percakapan bermasalah untuk review manual.
- **TC-AILOG-04:** Pengujian **Recommendation Logs - Index**: Tampilkan daftar rekomendasi destinasi yang diberikan AI.
- **TC-AILOG-05:** Pengujian **Recommendation Logs - Show**: Melihat detail rekomendasi (destinasi, alasan, user).
- **TC-AILOG-06:** Pengujian **Recommendation Logs - Export**: Export data log rekomendasi dalam format CSV/Excel.

---

## III. MODUL KONFIGURASI & ANALITIK (HIJAU)

### 1. Analitik Mendalam (Deep Analytics)
- **TC-ANLY-01:** Pengujian **Dashboard Analitik Utama**: Visualisasi KPI dan statistik keseluruhan sistem.
- **TC-ANLY-02:** Pengujian **Analytics Destinations**: Menampilkan destinasi paling banyak dilihat, diulas, dan dinilai tinggi.
- **TC-ANLY-03:** Pengujian **Analytics Events**: Menampilkan event dengan minat pengunjung tertinggi.
- **TC-ANLY-04:** Pengujian **Analytics Reports**: Menampilkan statistik laporan (tipe, status, trend).
- **TC-ANLY-05:** Pengujian **Filter by Date Range**: Memfilter data analitik berdasarkan periode waktu tertentu.
- **TC-ANLY-06:** Pengujian **Export Analytics**: Export data analitik dalam format report.

### 2. Pengaturan Sistem (System Settings - Super Admin Only)
- **TC-SET-01:** Pengujian **General Settings - View**: Menampilkan form pengaturan umum aplikasi.
- **TC-SET-02:** Pengujian **General Settings - Update**: Memperbarui nama aplikasi, logo, kontak, deskripsi.
- **TC-SET-03:** Pengujian **API Keys - View**: Menampilkan form pengaturan API keys pihak ketiga.
- **TC-SET-04:** Pengujian **API Keys - Update**: Memperbarui kunci API (Google Maps, Cloud Storage, dll).
- **TC-SET-05:** Pengujian **AI Configuration - View**: Menampilkan form pengaturan AI Chatbot.
- **TC-SET-06:** Pengujian **AI Configuration - Update**: Memperbarui model Gemini, prompt system, dan parameter AI.
- **TC-SET-07:** Pengujian **Maintenance Mode - Toggle**: Mengaktifkan/menonaktifkan mode maintenance global.
- **TC-SET-08:** Pengujian **Maintenance Mode - Access**: Verifikasi akses terbatas saat maintenance mode aktif.

### 3. Audit Logs (Super Admin Only)
- **TC-AUD-01:** Pengujian **Audit Logs - Index**: Tampilkan daftar semua aksi admin dengan filter.
- **TC-AUD-02:** Pengujian **Audit Logs - Show**: Melihat detail lengkap setiap aksi (user, action, timestamp, perubahan data).
- **TC-AUD-03:** Pengujian **Filter by User**: Menampilkan hanya audit logs dari user tertentu.
- **TC-AUD-04:** Pengujian **Filter by Action Type**: Menampilkan hanya aksi tertentu (create, update, delete, login).
- **TC-AUD-05:** Pengujian **Filter by Date Range**: Menampilkan logs dalam periode waktu tertentu.
- **TC-AUD-06:** Pengujian **Export Audit Logs**: Export audit logs untuk keperluan compliance.

### 4. Profil & Keamanan Admin (All Authenticated Users)
- **TC-PROF-01:** Pengujian **Edit Profile**: Menampilkan form edit profile admin.
- **TC-PROF-02:** Pengujian **Update Profile**: Memperbarui nama, email, foto profile admin.
- **TC-PROF-03:** Pengujian **Update Password**: Mengubah password admin dengan validasi password lama.
- **TC-PROF-04:** Pengujian **Password Strength**: Memvalidasi kekuatan password baru (minimum length, kompleksitas).

---

## IV. MODUL AUTHENTICATION & AUTHORIZATION (MERAH)

### 1. Login & Session Management
- **TC-AUTH-01:** Pengujian **Login Form**: Menampilkan form login dengan email dan password.
- **TC-AUTH-02:** Pengujian **Login Success**: Login dengan kredensial valid dan redirect ke dashboard.
- **TC-AUTH-03:** Pengujian **Login Failed**: Menampilkan error message untuk kredensial invalid.
- **TC-AUTH-04:** Pengujian **Session Timeout**: Memvalidasi sesi berakhir setelah periode inaktivitas.
- **TC-AUTH-05:** Pengujian **Session Persistence**: Memvalidasi sesi tetap aktif saat refresh halaman.
- **TC-AUTH-06:** Pengujian **Logout**: Menghapus sesi dan redirect ke halaman login.
- **TC-AUTH-07:** Pengujian **Logout Audit**: Memverifikasi aksi logout tercatat di audit logs.

### 2. Forgot & Reset Password
- **TC-AUTH-08:** Pengujian **Forgot Password Form**: Menampilkan form input email untuk reset password.
- **TC-AUTH-09:** Pengujian **Send Reset Link**: Mengirimkan link reset password ke email yang terdaftar.
- **TC-AUTH-10:** Pengujian **Reset Form**: Menampilkan form reset password dengan token valid.
- **TC-AUTH-11:** Pengujian **Reset Success**: Memperbarui password dengan validasi token.
- **TC-AUTH-12:** Pengujian **Invalid Token**: Menampilkan error untuk token expired atau invalid.

### 3. Role-Based Access Control (RBAC)
- **TC-RBAC-01:** Pengujian **Admin Role**: Akses penuh ke semua modul konten (destinations, events, carousel, dll).
- **TC-RBAC-02:** Pengujian **Super Admin Role**: Akses penuh termasuk settings dan audit logs.
- **TC-RBAC-03:** Pengujian **Moderator Role**: Akses terbatas ke reviews dan reports saja.
- **TC-RBAC-04:** Pengujian **Permission Denied**: Menampilkan halaman error 403 untuk akses unauthorized.
- **TC-RBAC-05:** Pengujian **Route Protection**: Memvalidasi middleware auth:admin pada setiap protected route.

---

## V. SKENARIO INTEGRASI & END-TO-END

### 1. Workflow Destinasi Lengkap
- **TC-E2E-01:** Membuat destinasi baru → Upload galeri → Tambah fasilitas → Publish → Verifikasi di mobile app.
- **TC-E2E-02:** Edit destinasi → Update galeri → Toggle featured → Verifikasi perubahan real-time.
- **TC-E2E-03:** Hapus destinasi → Verifikasi penghapusan dari trending → Audit log tercatat.

### 2. Workflow Konten Dinamis
- **TC-E2E-04:** Buat event → Tambah ke carousel → Set trending → Verifikasi tampilan.
- **TC-E2E-05:** Update berita/promosi → Verifikasi perubahan di aplikasi → Lihat di analytics.

### 3. Workflow Moderasi & AI
- **TC-E2E-06:** User berikan review → AI analisis sentimen → Admin approve/reject → Verifikasi hasil.
- **TC-E2E-07:** Report konten → Admin take action → Chatbot log flagged → Audit tercatat.

### 4. Workflow Administrator & Settings
- **TC-E2E-08:** Super admin update AI config → Verifikasi chatbot menggunakan konfigurasi baru.
- **TC-E2E-09:** Admin activity → Tercatat di audit logs → Dapat di-export untuk compliance.

### 5. Workflow Security & Audit
- **TC-E2E-10:** Admin login → Perform actions → Logout → Verifikasi audit logs → Export untuk dokumentasi.
- **TC-E2E-11:** Multiple admin akses → Role-based actions → Verify user-specific permissions → Check audit trail.

---

## VI. TEST CASE EDGE CASES & ERROR HANDLING

### 1. Input Validation
- **TC-EDGE-01:** Submit form destinasi tanpa nama/deskripsi → Validasi error ditampilkan.
- **TC-EDGE-02:** Upload gambar dengan format invalid → Error message ditampilkan.
- **TC-EDGE-03:** Input koordinat dengan format salah → Validasi format lokasi.
- **TC-EDGE-04:** Submit form dengan karakter special → Sanitasi dan verifikasi.

### 2. Data Integrity
- **TC-EDGE-05:** Hapus destinasi yang memiliki reviews → Verifikasi cascade delete atau soft delete.
- **TC-EDGE-06:** Update trending saat dalam automatic mode → Verifikasi tidak mempengaruhi data.
- **TC-EDGE-07:** Duplikasi konten (nama sama) → Validasi atau warning ditampilkan.

### 3. Performance & Load
- **TC-EDGE-08:** Index dengan 1000+ items → Verifikasi pagination berfungsi dengan baik.
- **TC-EDGE-09:** Export data besar (audit logs) → Verifikasi tidak timeout.
- **TC-EDGE-10:** Upload gallery dengan 50+ foto → Verifikasi upload dan sorting berfungsi.

### 4. Concurrency & Race Conditions
- **TC-EDGE-11:** Edit destinasi dari 2 tab/user berbeda → Verifikasi last-write-wins atau conflict detection.
- **TC-EDGE-12:** Update trending order saat mode automatic → Verifikasi konsistensi data.

---

## VII. SECURITY TEST CASES

### 1. Authentication Security
- **TC-SEC-01:** Akses protected route tanpa login → Redirect ke login.
- **TC-SEC-02:** Manipulasi session ID → Session invalid.
- **TC-SEC-03:** SQL Injection pada search field → Query aman dan tidak vulnerable.
- **TC-SEC-04:** XSS payload di form input → Payload di-sanitasi dan tidak execute.

### 2. Authorization Security
- **TC-SEC-05:** Moderator akses modul destinasi → Permission denied (403).
- **TC-SEC-06:** User dengan role biasa akses settings → Akses ditolak.
- **TC-SEC-07:** Manipulasi role di request → Validasi role dari session, bukan dari user input.

### 3. API Security
- **TC-SEC-08:** POST request tanpa CSRF token → Request ditolak.
- **TC-SEC-09:** Akses API tanpa authentication header → Response unauthorized (401).
- **TC-SEC-10:** Rate limiting pada login attempt → Akses dibatasi setelah N attempts.

---

## VIII. PERSIAPAN EXECUTION

### Tools & Environment:
- **Black-box Testing:** Katalon Studio, Postman, Selenium
- **White-box Testing:** PHPUnit Feature Tests, Laravel Testing
- **Test Data:** Database seeding dengan 89 test records
- **Browsers:** Chrome, Firefox, Safari, Edge
- **Devices:** Desktop, Tablet, Mobile

### Test Execution Schedule:
- Phase 1 (Week 1-2): Authentication, RBAC, Basic CRUD
- Phase 2 (Week 2-3): Advanced Features, AI Integration
- Phase 3 (Week 3-4): Integration & E2E Tests
- Phase 4 (Week 4-5): Security, Performance, Edge Cases
- Phase 5 (Week 5-6): Regression & Production Readiness

### Success Criteria:
- 100% test case execution completed
- 0 critical/high priority bugs remaining
- 95%+ test case pass rate
- All audit logs properly recorded
- Security vulnerabilities: 0 critical/high

---

**Status Pengujian Akhir:**
- ✅ Skenario telah divalidasi terhadap struktur kode di `routes/web.php` dan `routes/admin.php`
- ✅ Semua 23+ controllers telah dipetakan ke test cases
- ✅ Total 124 test cases mencakup: Functional, Integration, E2E, Security, dan Performance
- ✅ Semua modul siap diuji menggunakan metode Black-box (Katalon) maupun White-box (Feature Test)
- ✅ Edge cases dan error handling telah dipertimbangkan
- ✅ Role-based access control (Admin, Super Admin, Moderator) telah dicakup di RBAC tests

**Catatan Penting:**
- Pastikan semua controllers memiliki proper validation
- Implementasi audit logging di setiap critical action
- Verifikasi RBAC middleware di setiap protected route
- Test dengan data volume yang sesuai (pagination, performance)
- Validasi semua integration dengan third-party API (Google Maps, Cloud Storage, Gemini AI)
