# Admin Panel — User Flow Lengkap

```mermaid
flowchart TD

    START([Mulai]) --> OPEN[Akses /admin]
    OPEN --> CEK_SESI{Sesi aktif?}
    CEK_SESI -- Ya --> DASH
    CEK_SESI -- Tidak --> LOGIN

%% ═══════════════════════════════════════════════════════════
%% 1. AUTENTIKASI
%% ═══════════════════════════════════════════════════════════

    subgraph AUTH [Autentikasi]
        direction TB

        LOGIN[/Form Login\nInput email & password/]
        LOGIN --> CRED{Email & password valid?}
        CRED -- Tidak --> ERR1[Tampilkan pesan error]
        ERR1 --> LOGIN
        CRED -- Ya --> AKTIF{Akun aktif?}
        AKTIF -- Tidak --> ERR2[Akun dinonaktifkan]
        AKTIF -- Ya --> DASH

        LOGIN --> FORGOT[/Form Lupa Password/]
        FORGOT --> SUBMIT_EMAIL[Submit email]
        SUBMIT_EMAIL --> CEK_EMAIL{Email terdaftar?}
        CEK_EMAIL -- Tidak --> MSG_GENERIC[Tampilkan pesan generik\ntanpa reveal status email]
        MSG_GENERIC --> LOGIN
        CEK_EMAIL -- Ya --> TOKEN_SENT[Generate token & kirim ke email]
        TOKEN_SENT --> RESET[/Form Reset Password\nInput token + password baru/]
        RESET --> CEK_TOKEN{Token valid?\n& belum kedaluwarsa 60 menit?}
        CEK_TOKEN -- Tidak --> ERR3[Tampilkan: token tidak valid / kedaluwarsa]
        ERR3 --> FORGOT
        CEK_TOKEN -- Ya --> VALID_PASS{Password memenuhi syarat?\nupper + angka + simbol + min 8}
        VALID_PASS -- Tidak --> RESET
        VALID_PASS -- Ya --> SIMPAN_PASS[Simpan password baru\nHapus token]
        SIMPAN_PASS --> LOGIN
    end

%% ═══════════════════════════════════════════════════════════
%% 2. DASHBOARD
%% ═══════════════════════════════════════════════════════════

    DASH[/Dashboard\nStats ringkasan · Top 5 Destinasi\nTrip stats hari ini/minggu/bulan\nChart 12 bulan via AJAX/]

    DASH --> NAV{Pilih menu}

%% ═══════════════════════════════════════════════════════════
%% 3. DESTINASI
%% ═══════════════════════════════════════════════════════════

    NAV -- Destinasi --> DEST_LIST[/List Destinasi\nFilter: kategori · status · search\nSort & pagination/]

    DEST_LIST --> DEST_TAB{Pilih tab}

    DEST_TAB -- Kelola Destinasi --> DEST_AKSI{Aksi?}
    DEST_AKSI -- Tambah --> DEST_CREATE[/Form Tambah Destinasi\nNama · Deskripsi · Lokasi\nKategori · Koordinat · Fasilitas\nGambar/Video Thumbnail + Galeri/]
    DEST_CREATE --> DEST_VAL{Data valid?}
    DEST_VAL -- Tidak --> DEST_CREATE
    DEST_VAL -- Ya --> DEST_UPLOAD[Upload media ke Cloudinary/Lokal]
    DEST_UPLOAD --> DEST_SAVED[Destinasi tersimpan ke MongoDB]
    DEST_SAVED --> DEST_LIST

    DEST_AKSI -- Edit --> DEST_EDIT[/Form Edit Destinasi\nUbah data · Hapus/tambah gambar galeri\nUbah thumbnail/]
    DEST_EDIT --> DEST_EVAL{Data valid?}
    DEST_EVAL -- Tidak --> DEST_EDIT
    DEST_EVAL -- Ya --> DEST_UPDATED[Destinasi diperbarui]
    DEST_UPDATED --> DEST_LIST

    DEST_AKSI -- Hapus --> DEST_KONFIRM{Konfirmasi hapus?}
    DEST_KONFIRM -- Batal --> DEST_LIST
    DEST_KONFIRM -- Ya --> DEST_DELETE[Hapus destinasi + file media]
    DEST_DELETE --> DEST_LIST

    DEST_AKSI -- Toggle Status --> DEST_TOGGLE[Status aktif/nonaktif diperbarui]
    DEST_TOGGLE --> DEST_LIST

    DEST_AKSI -- Toggle Featured --> DEST_FEAT[Status featured diperbarui]
    DEST_FEAT --> DEST_LIST

    DEST_TAB -- Trending --> TREND_PAGE[/Tab Trending\nStats: destinasi · wishlist · review\nChart tren 7 hari/]

    TREND_PAGE --> TREND_MODE{Mode trending}
    TREND_MODE -- Otomatis --> TREND_AUTO[Sistem hitung otomatis\nberdasarkan rating & review]
    TREND_AUTO --> TREND_PAGE

    TREND_MODE -- Manual --> TREND_MANUAL[/Kelola Urutan Manual/]
    TREND_MANUAL --> TREND_AKSI{Aksi?}
    TREND_AKSI -- Cari destinasi --> TREND_SEARCH[AJAX autocomplete search]
    TREND_SEARCH --> TREND_ADD[Tambah ke list trending]
    TREND_ADD --> TREND_MANUAL
    TREND_AKSI -- Drag & drop urutan --> TREND_ORDER[Simpan urutan baru]
    TREND_ORDER --> TREND_MANUAL
    TREND_AKSI -- Hapus dari list --> TREND_REMOVE[Destinasi dihapus dari trending]
    TREND_REMOVE --> TREND_MANUAL
    TREND_AKSI -- Reset ke otomatis --> TREND_RESET[Mode diubah ke otomatis]
    TREND_RESET --> TREND_PAGE

    DEST_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 4. ACARA
%% ═══════════════════════════════════════════════════════════

    NAV -- Acara --> EVT_LIST[/List Acara\nFilter · sort · pagination/]
    EVT_LIST --> EVT_AKSI{Aksi?}

    EVT_AKSI -- Tambah --> EVT_CREATE[/Form Tambah Acara\nNama · Deskripsi · Tanggal · Lokasi\nGambar/]
    EVT_CREATE --> EVT_VAL{Data valid?}
    EVT_VAL -- Tidak --> EVT_CREATE
    EVT_VAL -- Ya --> EVT_SAVED[Acara tersimpan]
    EVT_SAVED --> EVT_LIST

    EVT_AKSI -- Edit --> EVT_EDIT[/Form Edit Acara/]
    EVT_EDIT --> EVT_EVAL{Data valid?}
    EVT_EVAL -- Tidak --> EVT_EDIT
    EVT_EVAL -- Ya --> EVT_UPDATED[Acara diperbarui]
    EVT_UPDATED --> EVT_LIST

    EVT_AKSI -- Hapus --> EVT_KONFIRM{Konfirmasi hapus?}
    EVT_KONFIRM -- Batal --> EVT_LIST
    EVT_KONFIRM -- Ya --> EVT_DELETE[Acara dihapus]
    EVT_DELETE --> EVT_LIST

    EVT_AKSI -- Toggle Status --> EVT_TOGGLE[Status aktif/nonaktif diperbarui]
    EVT_TOGGLE --> EVT_LIST

    EVT_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 5. CAROUSEL & BANNER
%% ═══════════════════════════════════════════════════════════

    NAV -- Carousel & Banner --> BAN_LIST[/List Carousel & Banner\nUrutan drag-drop\nSetting durasi autoplay/]
    BAN_LIST --> BAN_AKSI{Aksi?}

    BAN_AKSI -- Tambah --> BAN_CREATE[/Form Tambah Banner\nJudul · Subtitle · Kategori\nUpload gambar/video via Cloudinary\nTanggal tayang · Link konten/]
    BAN_CREATE --> BAN_VAL{Data valid?}
    BAN_VAL -- Tidak --> BAN_CREATE
    BAN_VAL -- Ya --> BAN_SAVED[Banner tersimpan]
    BAN_SAVED --> BAN_LIST

    BAN_AKSI -- Edit --> BAN_EDIT[/Form Edit Banner/]
    BAN_EDIT --> BAN_EVAL{Data valid?}
    BAN_EVAL -- Tidak --> BAN_EDIT
    BAN_EVAL -- Ya --> BAN_UPDATED[Banner diperbarui]
    BAN_UPDATED --> BAN_LIST

    BAN_AKSI -- Hapus --> BAN_KONFIRM{Konfirmasi hapus?}
    BAN_KONFIRM -- Batal --> BAN_LIST
    BAN_KONFIRM -- Ya --> BAN_DELETE[Banner + media dihapus]
    BAN_DELETE --> BAN_LIST

    BAN_AKSI -- Toggle Aktif --> BAN_TOGGLE[Status aktif/nonaktif diperbarui]
    BAN_TOGGLE --> BAN_LIST

    BAN_AKSI -- Drag & drop urutan --> BAN_ORDER[Urutan baru disimpan]
    BAN_ORDER --> BAN_LIST

    BAN_AKSI -- Setting autoplay --> BAN_SETTING[Input durasi autoplay\n1–60 detik]
    BAN_SETTING --> BAN_SAVED2[Setting disimpan]
    BAN_SAVED2 --> BAN_LIST

    BAN_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 6. FASILITAS UMUM
%% ═══════════════════════════════════════════════════════════

    NAV -- Fasilitas Umum --> FAS_LIST[/List Fasilitas Umum/]
    FAS_LIST --> FAS_AKSI{Aksi?}
    FAS_AKSI -- Tambah --> FAS_CREATE[/Form Tambah Fasilitas/]
    FAS_CREATE --> FAS_VAL{Data valid?}
    FAS_VAL -- Tidak --> FAS_CREATE
    FAS_VAL -- Ya --> FAS_SAVED[Fasilitas tersimpan]
    FAS_SAVED --> FAS_LIST
    FAS_AKSI -- Edit --> FAS_EDIT[/Form Edit Fasilitas/]
    FAS_EDIT --> FAS_EVAL{Data valid?}
    FAS_EVAL -- Tidak --> FAS_EDIT
    FAS_EVAL -- Ya --> FAS_UPDATED[Fasilitas diperbarui]
    FAS_UPDATED --> FAS_LIST
    FAS_AKSI -- Hapus --> FAS_KONFIRM{Konfirmasi?}
    FAS_KONFIRM -- Ya --> FAS_LIST
    FAS_KONFIRM -- Batal --> FAS_LIST
    FAS_AKSI -- Toggle Status --> FAS_TOGGLE[Status diperbarui]
    FAS_TOGGLE --> FAS_LIST
    FAS_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 7. BERITA & PROMOSI
%% ═══════════════════════════════════════════════════════════

    NAV -- Berita & Promosi --> BER_LIST[/List Berita & Promosi/]
    BER_LIST --> BER_AKSI{Aksi?}
    BER_AKSI -- Tambah --> BER_CREATE[/Form Tambah Berita/Promo/]
    BER_CREATE --> BER_VAL{Data valid?}
    BER_VAL -- Tidak --> BER_CREATE
    BER_VAL -- Ya --> BER_SAVED[Tersimpan]
    BER_SAVED --> BER_LIST
    BER_AKSI -- Edit --> BER_EDIT[/Form Edit/]
    BER_EDIT --> BER_EVAL{Data valid?}
    BER_EVAL -- Tidak --> BER_EDIT
    BER_EVAL -- Ya --> BER_UPDATED[Diperbarui]
    BER_UPDATED --> BER_LIST
    BER_AKSI -- Hapus --> BER_KONFIRM{Konfirmasi?}
    BER_KONFIRM -- Ya --> BER_LIST
    BER_KONFIRM -- Batal --> BER_LIST
    BER_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 8. BUDAYA & WARISAN
%% ═══════════════════════════════════════════════════════════

    NAV -- Budaya & Warisan --> BUD_LIST[/List Budaya & Warisan/]
    BUD_LIST --> BUD_AKSI{Aksi?}
    BUD_AKSI -- Tambah --> BUD_CREATE[/Form Tambah/]
    BUD_CREATE --> BUD_VAL{Data valid?}
    BUD_VAL -- Tidak --> BUD_CREATE
    BUD_VAL -- Ya --> BUD_SAVED[Tersimpan]
    BUD_SAVED --> BUD_LIST
    BUD_AKSI -- Edit --> BUD_EDIT[/Form Edit/]
    BUD_EDIT --> BUD_EVAL{Data valid?}
    BUD_EVAL -- Tidak --> BUD_EDIT
    BUD_EVAL -- Ya --> BUD_UPDATED[Diperbarui]
    BUD_UPDATED --> BUD_LIST
    BUD_AKSI -- Hapus --> BUD_KONFIRM{Konfirmasi?}
    BUD_KONFIRM -- Ya --> BUD_LIST
    BUD_KONFIRM -- Batal --> BUD_LIST
    BUD_AKSI -- Toggle Status --> BUD_TOGGLE[Status diperbarui]
    BUD_TOGGLE --> BUD_LIST
    BUD_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 9. PENGGUNA
%% ═══════════════════════════════════════════════════════════

    NAV -- Pengguna --> USR_LIST[/List Pengguna\nFilter: nama · email · role · status\nSort & pagination\nStats: total · aktif · suspended · guest/]
    USR_LIST --> USR_AKSI{Aksi?}

    USR_AKSI -- Lihat Aktivitas --> USR_ACT[/Modal Aktivitas User\nUlasan · Trip plans · Wishlist\nStats: jumlah review · trip · wishlist/]
    USR_ACT --> USR_LIST

    USR_AKSI -- Aktifkan Akun --> USR_AKTIF[Status akun diaktifkan\nData suspend dihapus]
    USR_AKTIF --> USR_LIST

    USR_AKSI -- Suspend Akun --> USR_SUSPEND[/Form Suspend\nPilih kategori: Spammer · Abuse\nFraud · Inappropriate · Other\nInput alasan suspend/]
    USR_SUSPEND --> USR_SUSPEND_VAL{Data valid?}
    USR_SUSPEND_VAL -- Tidak --> USR_SUSPEND
    USR_SUSPEND_VAL -- Ya --> USR_SUSPENDED[Akun disuspend\nKategori & alasan tersimpan]
    USR_SUSPENDED --> USR_LIST

    USR_AKSI -- Export CSV --> USR_EXPORT[Download file CSV pengguna]
    USR_EXPORT --> USR_LIST

    USR_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 10. ULASAN
%% ═══════════════════════════════════════════════════════════

    NAV -- Ulasan --> RV_LIST[/List Ulasan\nFilter: rating · destinasi · sentimen · search\nSort & pagination\nStats: total · distribusi rating · sentimen/]

    RV_LIST --> RV_AKSI{Aksi?}

    RV_AKSI -- Lihat Detail --> RV_DETAIL[/Detail Ulasan\nTeks review · rating\nInfo user · destinasi\nHasil sentimen/]
    RV_DETAIL --> RV_DETAIL_AKSI{Aksi dari detail?}
    RV_DETAIL_AKSI -- Kembali --> RV_LIST

    RV_AKSI -- Approve --> RV_APPROVE[Status ulasan: Approved\nUlasan terkunci dari hapus]
    RV_APPROVE --> RV_LIST

    RV_AKSI -- Reject --> RV_REJECT[Status ulasan: Rejected]
    RV_REJECT --> RV_LIST

    RV_AKSI -- Analisis Sentimen Single --> RV_ANALYZE[Kirim teks ke AI service]
    RV_ANALYZE --> RV_AI_RESULT{AI berhasil & lolos quality gate?}
    RV_AI_RESULT -- Tidak --> RV_AI_FAIL[Tampilkan error analisis]
    RV_AI_FAIL --> RV_LIST
    RV_AI_RESULT -- Ya --> RV_SENT_SAVED[Simpan: label · confidence\nscores · reason · model version]
    RV_SENT_SAVED --> RV_LIST

    RV_AKSI -- Analisis Batch --> RV_BATCH[/Pilih jumlah\nmaks 100 ulasan pending/]
    RV_BATCH --> RV_BATCH_SEND[Kirim batch ke AI service]
    RV_BATCH_SEND --> RV_BATCH_RESULT{Batch berhasil?}
    RV_BATCH_RESULT -- Tidak --> RV_BATCH_FAIL[Tampilkan error]
    RV_BATCH_FAIL --> RV_LIST
    RV_BATCH_RESULT -- Ya --> RV_BATCH_SAVED[Update semua ulasan yang lolos quality gate]
    RV_BATCH_SAVED --> RV_LIST

    RV_AKSI -- Hapus --> RV_CEK_STATUS{Ulasan sudah approved?}
    RV_CEK_STATUS -- Ya --> RV_LOCKED[Tidak dapat dihapus\nTampilkan pesan error]
    RV_LOCKED --> RV_LIST
    RV_CEK_STATUS -- Tidak --> RV_KONFIRM{Konfirmasi hapus?}
    RV_KONFIRM -- Batal --> RV_LIST
    RV_KONFIRM -- Ya --> RV_SOFT_DEL[Soft delete\nTandai is_deleted · simpan audit trail]
    RV_SOFT_DEL --> RV_LIST

    RV_AKSI -- Export CSV --> RV_EXPORT[Download file CSV ulasan\nsesuai filter aktif]
    RV_EXPORT --> RV_LIST

    RV_AKSI -- Print Analytics --> RV_PRINT[/Halaman cetak analitik ulasan\nDistribusi rating · sentimen · tren 6 bulan/]
    RV_PRINT --> RV_LIST

    RV_AKSI -- Tab Analytics --> RV_ANALYTICS[/Tab Analitik\nChart sentimen · rating · tren bulanan\nTop destinasi berdasarkan ulasan\nKeyword summary dari AI/]
    RV_ANALYTICS --> RV_FILTER_AN[Filter: destinasi · tanggal · rating · sentimen\nStats diperbarui via AJAX]
    RV_FILTER_AN --> RV_ANALYTICS
    RV_ANALYTICS --> RV_LIST

    RV_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 11. LAPORAN & ISU
%% ═══════════════════════════════════════════════════════════

    NAV -- Laporan & Isu --> RP_LIST[/List Laporan\nFilter: status · alasan · destinasi · tanggal\nSort & pagination/]

    RP_LIST --> RP_AKSI{Aksi?}

    RP_AKSI -- Lihat Detail --> RP_DETAIL[/Detail Laporan\nInfo pelapor · destinasi · alasan\nFoto bukti · riwayat tindakan/]
    RP_DETAIL --> RP_DETAIL_AKSI{Aksi dari detail?}

    RP_DETAIL_AKSI -- Update Status --> RP_STATUS[/Pilih status: pending · reviewed · resolved\nNB: resolved terkunci tidak bisa diubah lagi/]
    RP_STATUS --> RP_STATUS_CEK{Laporan sudah resolved?}
    RP_STATUS_CEK -- Ya --> RP_LOCKED[Tampilkan: laporan terkunci]
    RP_LOCKED --> RP_DETAIL
    RP_STATUS_CEK -- Tidak --> RP_STATUS_SAVED[Status diperbarui]
    RP_STATUS_SAVED --> RP_DETAIL

    RP_DETAIL_AKSI -- Ambil Tindakan --> RP_ACTION[/Form Tindakan\nPilih: Hapus Konten · Peringatkan User · Abaikan\nInput alasan tindakan/]
    RP_ACTION --> RP_ACTION_CEK{Laporan sudah resolved?}
    RP_ACTION_CEK -- Ya --> RP_LOCKED2[Tampilkan: laporan terkunci]
    RP_LOCKED2 --> RP_DETAIL
    RP_ACTION_CEK -- Tidak --> RP_ACTION_VAL{Data valid?}
    RP_ACTION_VAL -- Tidak --> RP_ACTION
    RP_ACTION_VAL -- Ya --> RP_ACTION_SAVED[Tindakan dicatat\nStatus otomatis jadi resolved]
    RP_ACTION_SAVED --> RP_LIST

    RP_DETAIL_AKSI -- Resolve --> RP_RESOLVE[Status diubah ke resolved]
    RP_RESOLVE --> RP_LIST

    RP_DETAIL_AKSI -- Flag --> RP_FLAG[Laporan ditandai flag]
    RP_FLAG --> RP_DETAIL

    RP_DETAIL_AKSI -- Kembali --> RP_LIST

    RP_AKSI -- Hapus --> RP_KONFIRM{Konfirmasi hapus?}
    RP_KONFIRM -- Batal --> RP_LIST
    RP_KONFIRM -- Ya --> RP_DELETE[Laporan dihapus dari MongoDB]
    RP_DELETE --> RP_LIST

    RP_AKSI -- Export --> RP_EXPORT[Download CSV / Excel\nsesuai filter aktif]
    RP_EXPORT --> RP_LIST

    RP_AKSI -- Print Laporan --> RP_PRINT[/Halaman cetak format resmi\nKonfigurasi: instansi · nomor surat\nnama penandatangan · logo/]
    RP_PRINT --> RP_LIST

    RP_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 12. LOG CHATBOT
%% ═══════════════════════════════════════════════════════════

    NAV -- Log Chatbot --> CH_LIST[/List Sesi Chatbot\nFilter: tipe user · search nama/email\nStats: total · user · guest/]
    CH_LIST --> CH_AKSI{Aksi?}

    CH_AKSI -- Lihat Detail --> CH_DETAIL[/Detail Sesi\nRiwayat pesan user & chatbot/]
    CH_DETAIL --> CH_LIST

    CH_AKSI -- Flag Sesi --> CH_FLAG[Tampilkan info: fitur belum tersedia]
    CH_FLAG --> CH_LIST

    CH_AKSI -- Export CSV --> CH_EXPORT[Download CSV sesi chatbot\nsesuai filter aktif]
    CH_EXPORT --> CH_LIST

    CH_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 13. LOG REKOMENDASI
%% ═══════════════════════════════════════════════════════════

    NAV -- Log Rekomendasi --> RC_LIST[/List Log Rekomendasi\nFilter & pagination/]
    RC_LIST --> RC_AKSI{Aksi?}
    RC_AKSI -- Lihat Detail --> RC_DETAIL[/Detail Log Rekomendasi/]
    RC_DETAIL --> RC_LIST
    RC_AKSI -- Export CSV --> RC_EXPORT[Download CSV]
    RC_EXPORT --> RC_LIST
    RC_LIST --> NAV

%% ═══════════════════════════════════════════════════════════
%% 14. ANALYTICS
%% ═══════════════════════════════════════════════════════════

    NAV -- Analytics --> AN_DASH[/Overview Analytics\nTotal views · pencarian · pengguna aktif/]
    AN_DASH --> AN_NAV{Lihat analitik?}
    AN_NAV -- Destinasi --> AN_DEST[/Analitik Destinasi\nTotal · aktif · featured · trending/]
    AN_DEST --> AN_DASH
    AN_NAV -- Acara --> AN_EVT[/Analitik Acara\nTotal · aktif/]
    AN_EVT --> AN_DASH
    AN_NAV -- Laporan --> AN_RPT[/Analitik Laporan\nTotal · pending · resolved/]
    AN_RPT --> AN_DASH
    AN_DASH --> NAV

%% ═══════════════════════════════════════════════════════════
%% 15. PENGATURAN
%% ═══════════════════════════════════════════════════════════

    NAV -- Pengaturan --> SET_PAGE[/Halaman Pengaturan Sistem/]
    SET_PAGE --> SET_AKSI{Aksi?}

    SET_AKSI -- General Settings --> SET_FORM[/Form General Settings\nUpload logo & favicon\nWarna primer & sekunder\nBahasa default\nToggle: enable reviews · reports\nmoderate reviews\nNotifikasi: review · report · user · error\nDark mode/]
    SET_FORM --> SET_VAL{Data valid?}
    SET_VAL -- Tidak --> SET_FORM
    SET_VAL -- Ya --> SET_SAVED[Pengaturan disimpan\nLog aktivitas dicatat]
    SET_SAVED --> SET_PAGE

    SET_AKSI -- Audit Log --> AUDIT_LIST[/List Audit Log\nFilter: aksi · user · tanggal/]
    AUDIT_LIST --> AUDIT_DETAIL[/Detail Audit Log\nData lama vs data baru/]
    AUDIT_DETAIL --> AUDIT_LIST
    AUDIT_LIST --> SET_PAGE

    SET_PAGE --> NAV

%% ═══════════════════════════════════════════════════════════
%% 16. PROFIL & AKUN
%% ═══════════════════════════════════════════════════════════

    NAV -- Profil --> PROF_PAGE[/Halaman Profil Admin/]
    PROF_PAGE --> PROF_AKSI{Aksi?}

    PROF_AKSI -- Update Profil --> PROF_FORM[/Form Edit Profil\nNama · Email · Foto profil/]
    PROF_FORM --> PROF_VAL{Data valid?\nemail unik?}
    PROF_VAL -- Tidak --> PROF_FORM
    PROF_VAL -- Ya --> PROF_SAVED[Profil diperbarui\nFoto lama dihapus dari storage]
    PROF_SAVED --> PROF_PAGE

    PROF_AKSI -- Ganti Password --> PASS_FORM[/Form Ganti Password\nPassword saat ini · Password baru · Konfirmasi/]
    PASS_FORM --> PASS_VAL{Password saat ini benar?\nPassword baru valid?}
    PASS_VAL -- Tidak --> PASS_FORM
    PASS_VAL -- Ya --> PASS_SAVED[Password diperbarui]
    PASS_SAVED --> PROF_PAGE

    PROF_PAGE --> NAV

%% ═══════════════════════════════════════════════════════════
%% 17. GLOBAL SEARCH
%% ═══════════════════════════════════════════════════════════

    NAV -- Global Search --> SEARCH[/Halaman Search\nInput kata kunci\nHasil dari semua modul/]
    SEARCH --> NAV

%% ═══════════════════════════════════════════════════════════
%% 18. LOGOUT
%% ═══════════════════════════════════════════════════════════

    NAV -- Logout --> LOGOUT_KONFIRM{Konfirmasi logout?}
    LOGOUT_KONFIRM -- Batal --> DASH
    LOGOUT_KONFIRM -- Ya --> LOGOUT_EXEC[Hapus sesi auth guard\nInvalidate session\nRegenerate CSRF token]
    LOGOUT_EXEC --> END([Selesai\nKembali ke Login])

%% ═══════════════════════════════════════════════════════════
%% STYLES
%% ═══════════════════════════════════════════════════════════

    style START           fill:#22c55e,stroke:#15803d,color:#fff
    style END             fill:#ef4444,stroke:#b91c1c,color:#fff
    style DASH            fill:#3b82f6,stroke:#1d4ed8,color:#fff
    style NAV             fill:#e0f2fe,stroke:#0284c7,color:#0c4a6e
    style ERR1            fill:#fecaca,stroke:#dc2626
    style ERR2            fill:#fecaca,stroke:#dc2626
    style ERR3            fill:#fecaca,stroke:#dc2626
    style RV_LOCKED       fill:#fecaca,stroke:#dc2626
    style RP_LOCKED       fill:#fecaca,stroke:#dc2626
    style RP_LOCKED2      fill:#fecaca,stroke:#dc2626
    style RV_AI_FAIL      fill:#fecaca,stroke:#dc2626
    style RV_BATCH_FAIL   fill:#fecaca,stroke:#dc2626
    style CEK_SESI        fill:#fef08a,stroke:#ca8a04
    style CRED            fill:#fef08a,stroke:#ca8a04
    style AKTIF           fill:#fef08a,stroke:#ca8a04
    style CEK_TOKEN       fill:#fef08a,stroke:#ca8a04
    style VALID_PASS      fill:#fef08a,stroke:#ca8a04
    style LOGOUT_KONFIRM  fill:#fef08a,stroke:#ca8a04
```
