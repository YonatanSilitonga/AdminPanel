@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')
@section('navbar_title', 'Pengguna')
@section('page_title', 'Manajemen Pengguna')
@section('page_description', 'Kelola data pengguna, hak akses, dan lihat riwayat aktivitas pengguna')

@section('page_actions')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.users.export', request()->query()) }}" class="flex items-center gap-2 px-8 py-3 bg-emerald-700 text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-emerald-700/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Export CSV
    </a>
    <div class="relative group cursor-pointer inline-flex items-center">
        <svg class="w-4 h-4 text-gray-400 hover:text-emerald-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
            <div class="space-y-2">
                <div>
                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Export CSV</span>
                    <p class="text-slate-200 font-sans leading-relaxed">Mengekspor daftar seluruh data akun pengguna (nama, email, status keaktifan, tanggal bergabung) ke dalam berkas CSV (Excel compatible) berdasarkan filter aktif saat ini.</p>
                </div>
            </div>
            <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
        </div>
    </div>
</div>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Pengguna & Akses</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Manajemen Pengguna</span>
</nav>
@endsection

@section('content')

<div x-data="{
    showDetailModal: false,
    showSuccessModal: false,
    loadingDetail: false,
    detailUser: null,
    successMessage: '',
    statusLoading: false,
    showConfirmModal: false,
    confirmTitle: '',
    confirmText: '',
    confirmCallback: null,
    confirmType: 'suspend',
    
    getInitials(name) {
        if (!name) return 'U';
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    },

    async openUserDetail(userBase) {
        this.showDetailModal = true;
        this.loadingDetail = true;
        
        try {
            const response = await fetch(`/admin/users/${userBase.id}/activity`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const fullData = await window.safeParseJSON(response);
            this.detailUser = fullData;
        } catch (error) {
            console.error('Error:', error);
            window.showAlert('Gagal memuat detail.', 'Error', 'error');
        } finally {
            this.loadingDetail = false;
        }
    },

    async toggleStatus(userId) {
        if (!userId) return;
        
        const isCurrentlyActive = this.detailUser && this.detailUser.user ? this.detailUser.user.is_active : true;
        const userName = this.detailUser && this.detailUser.user ? this.detailUser.user.name : 'pengguna';
        
        this.confirmType = isCurrentlyActive ? 'suspend' : 'activate';
        this.confirmTitle = isCurrentlyActive ? 'Suspend Pengguna' : 'Aktifkan Pengguna';
        this.confirmText = isCurrentlyActive 
            ? `Apakah Anda yakin ingin menangguhkan (suspend) akun '${userName}'? Akses masuk pengguna tersebut akan dibatasi.` 
            : `Apakah Anda yakin ingin mengaktifkan kembali akun '${userName}'?`;
            
        this.confirmCallback = async () => {
            this.statusLoading = true;
            try {
                const response = await fetch(`/admin/users/${userId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const result = await window.safeParseJSON(response);
                
                if (result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Status akun pengguna berhasil diubah.');
                    window.location.reload();
                } else {
                    window.showAlert(result.message || 'Gagal mengubah status akun.', 'Gagal', 'error');
                }
            } catch (error) {
                window.showAlert('Terjadi kesalahan saat mengubah status.', 'Error', 'error');
            } finally {
                this.statusLoading = false;
                this.showConfirmModal = false;
            }
        };
        
        this.showConfirmModal = true;
    }
}">

    <!-- Stats Overview -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-8 mb-8 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="flex items-center gap-4 px-6 first:pl-0">
                <div class="w-1 h-10 bg-emerald-700 rounded-full"></div>
                <div>
                    <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($stats['total'] ?? 0) }}</p>
                    <div class="flex items-center gap-1.5">
                        <p class="text-[13px] font-bold text-gray-400">Total</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Menampilkan akumulasi jumlah seluruh akun pengguna yang terdaftar di sistem database.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-sans">Dashboard Utama dan Halaman Analitik Pengguna.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 px-8">
                <div class="w-1 h-10 bg-emerald-500 rounded-full"></div>
                <div>
                    <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($stats['active'] ?? 0) }}</p>
                    <div class="flex items-center gap-1.5">
                        <p class="text-[13px] font-bold text-gray-400">Aktif Hari Ini</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Menunjukkan jumlah pengguna yang melakukan login atau interaksi aktif dalam 24 jam terakhir.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-sans">Dashboard Analitik Aktivitas Harian.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 px-8">
                <div class="w-1 h-10 bg-orange-400 rounded-full"></div>
                <div>
                    <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($stats['guests'] ?? 0) }}</p>
                    <div class="flex items-center gap-1.5">
                        <p class="text-[13px] font-bold text-gray-400">Guest Sessions</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Menghitung jumlah sesi pengunjung tanpa login (tamu) yang menjelajahi aplikasi.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-sans">Laporan statistik lalu lintas pengguna non-terdaftar.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 px-8 last:pr-0">
                <div class="w-1 h-10 bg-red-400 rounded-full"></div>
                <div>
                    <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($stats['suspended'] ?? 0) }}</p>
                    <div class="flex items-center gap-1.5">
                        <p class="text-[13px] font-bold text-gray-400">Suspended</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Menghitung jumlah akun pengguna yang dibekukan karena pelanggaran atau kebijakan sistem.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-sans">Manajemen Admin Panel untuk pengawasan pemblokiran akses.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <!-- Persist current sorting -->
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <!-- Cari Pengguna -->
                <div class="space-y-2 sm:col-span-6 lg:col-span-3">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Cari Pengguna
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Mencari pengguna berdasarkan nama lengkap atau alamat email.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                        <p class="text-slate-200 font-sans">Proses pencarian dan pemfilteran data tabel pengguna.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..." 
                            class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-[#066466] outline-none text-[14px] font-medium placeholder-gray-400 transition-all shadow-sm">
                    </div>
                </div>

                <!-- Status -->
                <div class="space-y-2 sm:col-span-6 lg:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Status Akun
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Menyaring pengguna berdasarkan status akun (Aktif atau Suspended).</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                        <p class="text-slate-200 font-sans">Kontrol status hak masuk/akses pengguna ke aplikasi mobile.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <select name="status" onchange="this.form.submit()" 
                        class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Suspended</option>
                    </select>
                </div>

                <!-- Rentang Tanggal Bergabung -->
                <div class="space-y-2 sm:col-span-12 md:col-span-8 lg:col-span-4">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Tanggal Bergabung
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Menyaring daftar pengguna berdasarkan rentang tanggal akun tersebut dibuat (bergabung).</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                        <p class="text-slate-200 font-sans">Pemantauan pertumbuhan pengguna baru dalam rentang waktu tertentu.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" 
                            class="flex-1 min-w-0 px-3 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[13px] font-medium text-gray-500 shadow-sm focus:border-[#066466] transition-all">
                        <span class="text-xs text-gray-400 font-bold flex-shrink-0">s/d</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" 
                            class="flex-1 min-w-0 px-3 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[13px] font-medium text-gray-500 shadow-sm focus:border-[#066466] transition-all">
                    </div>
                </div>

                <!-- Tampilkan & Reset -->
                <div class="space-y-2 sm:col-span-12 md:col-span-4 lg:col-span-3">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Tampilkan
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-sans">Mengatur jumlah baris pengguna yang ditampilkan dalam satu halaman tabel.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                        <p class="text-slate-200 font-sans">Navigasi halaman (pagination) tabel pengguna.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <div class="flex items-center gap-2">
                        <select name="per_page" onchange="this.form.submit()" 
                            class="flex-1 min-w-0 px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                            @foreach([10, 20, 50, 100] as $val)
                                <option value="{{ $val }}" @selected(request('per_page', 10) == $val)>{{ $val }}</option>
                            @endforeach
                        </select>
                        @if(request('search') || request('status') || request('start_date') || request('end_date') || request('per_page') != 10)
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white border-b border-gray-50">
                        @php
                            $sortOrder = request('sort_order') === 'asc' ? 'desc' : 'asc';
                            $currentSort = request('sort_by', 'created_at');
                        @endphp
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($currentSort === 'name' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Nama Pengguna
                                <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'name' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => ($currentSort === 'email' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Kontak
                                <svg class="w-4 h-4 {{ $currentSort === 'email' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'email' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'role', 'sort_order' => ($currentSort === 'role' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Role
                                <svg class="w-4 h-4 {{ $currentSort === 'role' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'role' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_active', 'sort_order' => ($currentSort === 'is_active' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Status
                                    <svg class="w-4 h-4 {{ $currentSort === 'is_active' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'is_active' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Menampilkan status aktif/tidaknya akun pengguna. Akun suspended tidak dapat masuk ke sistem.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                <p class="text-slate-200 font-sans">Aplikasi mobile saat proses autentikasi (login) pengguna.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($currentSort === 'created_at' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Bergabung
                                <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'created_at' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-sidebar-active/10 flex items-center justify-center text-sidebar-active font-bold shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[15px] font-bold text-gray-800 max-w-[150px] truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">ID: {{ substr($user->_id, -8) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 text-sm text-gray-600 max-w-[180px] truncate">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7 8.9a2.2 2.2 0 003.3 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $user->email }}
                                </div>
                                @if(isset($user->phone))
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $user->phone }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1 bg-gray-50 text-gray-500 rounded-lg text-xs font-bold uppercase tracking-wider">{{ $user->role ?? 'user' }}</span>
                        </td>
                        <td class="px-10 py-6">
                            @if($user->is_active)
                                <span class="px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] text-xs font-bold rounded-xl">Aktif</span>
                            @else
                                <span class="px-4 py-1.5 bg-red-50 text-red-500 text-xs font-bold rounded-xl">Suspended</span>
                            @endif
                        </td>
                        <td class="px-10 py-6">
                            <div class="text-[13px] text-gray-500 font-medium">{{ $user->created_at?->format('d M Y') ?? '-' }}</div>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button @click="openUserDetail({ id: '{{ $user->_id }}' })" 
                                    class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all"
                                    title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center text-gray-400">Belum ada pengguna.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 flex items-center justify-between bg-white">
            <p class="text-[13px] text-gray-400 font-medium">Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</p>
            <div class="flex items-center gap-2">
                @if($users->onFirstPage())
                    <span class="px-4 py-2 text-[13px] font-bold text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 text-[13px] font-bold text-gray-600 bg-gray-100 hover:bg-emerald-600 hover:text-white rounded-lg transition-all">Prev</a>
                @endif
                
                <div class="flex items-center gap-1">
                    @foreach($users->getUrlRange(max(1, $users->currentPage()-1), min($users->lastPage(), $users->currentPage()+1)) as $page => $url)
                        <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center text-[13px] font-bold {{ $page == $users->currentPage() ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-700/30' : 'text-gray-500 hover:bg-gray-100' }} rounded-lg transition-all">{{ $page }}</a>
                    @endforeach
                </div>

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 text-[13px] font-bold text-gray-600 bg-gray-100 hover:bg-emerald-600 hover:text-white rounded-lg transition-all">Next</a>
                @else
                    <span class="px-4 py-2 text-[13px] font-bold text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Detail User Modal -->
    <template x-if="showDetailModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click="showDetailModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            
            <div class="relative w-full max-w-[580px] bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] flex flex-col">
                <!-- Header (Fixed) -->
                <div class="px-8 py-6 flex items-center justify-between border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-900">Detail Pengguna</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Scrollable Content Area -->
                <div class="p-10 overflow-y-auto custom-scrollbar flex-1">
                    <div x-show="loadingDetail" class="flex flex-col items-center py-10">
                        <div class="w-10 h-10 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                    </div>

                    <div x-show="!loadingDetail && detailUser" class="space-y-10">
                        <!-- Profile Section -->
                        <div class="flex flex-col items-center text-center">
                            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center border-4 border-white shadow-xl mb-4 relative group">
                                <span class="text-3xl font-bold text-emerald-700" x-text="detailUser.initials"></span>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900" x-text="detailUser.user.name"></h4>
                            <p class="text-sm font-medium text-gray-400 mb-5" x-text="detailUser.user.email"></p>
                            <span class="px-5 py-2 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100" 
                                  x-text="detailUser.user.is_active ? 'AKTIF' : 'SUSPENDED'"></span>
                        </div>

                        <!-- Mini Stats Grid -->
                        <div class="grid grid-cols-3 gap-5">
                            <div class="bg-gray-50/50 rounded-[1.5rem] p-6 text-center border border-gray-100 transition-all hover:bg-white hover:shadow-xl hover:shadow-gray-100 group">
                                <p class="text-2xl font-bold text-gray-900 mb-0.5 group-hover:scale-110 transition-transform" x-text="detailUser.stats.reviews"></p>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Review</p>
                            </div>
                            <div class="bg-gray-50/50 rounded-[1.5rem] p-6 text-center border border-gray-100 transition-all hover:bg-white hover:shadow-xl hover:shadow-gray-100 group">
                                <p class="text-2xl font-bold text-gray-900 mb-0.5 group-hover:scale-110 transition-transform" x-text="detailUser.stats.trips"></p>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Trip</p>
                            </div>
                            <div class="bg-gray-50/50 rounded-[1.5rem] p-6 text-center border border-gray-100 transition-all hover:bg-white hover:shadow-xl hover:shadow-gray-100 group">
                                <p class="text-2xl font-bold text-gray-900 mb-0.5 group-hover:scale-110 transition-transform" x-text="detailUser.stats.wishlists"></p>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Wishlist</p>
                            </div>
                        </div>

                        <!-- Activity Timeline -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-2 mb-2 px-1">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <h5 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Riwayat Aktivitas</h5>
                            </div>

                            <div class="relative space-y-8 pl-1">
                                <div class="absolute left-[21px] top-4 bottom-4 w-[1.5px] bg-gray-100"></div>
                                <template x-for="activity in detailUser.activities">
                                    <div class="relative flex items-center gap-5">
                                        <div class="relative z-10 w-11 h-11 bg-white rounded-xl flex items-center justify-center border border-gray-100 shadow-sm text-emerald-600 transition-transform hover:scale-110">
                                            <template x-if="activity.icon === 'map'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg></template>
                                            <template x-if="activity.icon === 'chat'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg></template>
                                            <template x-if="activity.icon === 'heart'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg></template>
                                            <template x-if="activity.icon === 'search'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></template>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 leading-tight mb-0.5" x-text="activity.title"></p>
                                            <p class="text-xs font-medium text-gray-400 uppercase tracking-tighter" x-text="activity.time"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Footer Action -->
                        <div class="flex justify-center pt-6 border-t border-gray-50">
                            <button @click="toggleStatus(detailUser.user.id || detailUser.user._id)" 
                                :disabled="statusLoading"
                                :class="detailUser.user.is_active ? 'bg-orange-500 hover:bg-orange-600 shadow-orange-500/20' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20'"
                                class="flex items-center gap-3 px-12 py-4 text-white rounded-2xl font-bold text-sm transition-all shadow-lg disabled:opacity-50">
                                
                                <template x-if="statusLoading">
                                    <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                </template>
                                
                                <template x-if="!statusLoading">
                                    <div class="flex items-center gap-3">
                                        <template x-if="detailUser.user.is_active">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        </template>
                                        <template x-if="!detailUser.user.is_active">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </template>
                                        <span x-text="detailUser.user.is_active ? 'Suspend Akun Pengguna' : 'Aktifkan Akun Pengguna'"></span>
                                    </div>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Success Modal -->
    <template x-if="showSuccessModal">
        <div class="fixed inset-0 z-[200] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-[420px] bg-white rounded-[2rem] p-12 text-center animate-in zoom-in duration-300 shadow-2xl">
                <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner ring-4 ring-emerald-50/50">
                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Berhasil!</h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed" x-text="successMessage"></p>
            </div>
        </div>
    </template>

    <!-- Custom Confirm Modal -->
    <template x-if="showConfirmModal">
        <div class="fixed inset-0 z-[250] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <!-- Backdrop -->
                <div @click="showConfirmModal = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div class="relative w-full max-w-md bg-white shadow-2xl rounded-[2rem] text-gray-800 overflow-hidden z-10 animate-in zoom-in duration-300 max-h-[90vh] overflow-y-auto custom-scrollbar">
                    <div class="px-8 py-6 text-center mt-4">
                        <!-- Suspend Icon (Red/Orange Warning) -->
                        <template x-if="confirmType === 'suspend'">
                            <div class="w-20 h-20 bg-[#FEE2E2] rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </template>

                        <!-- Activate Icon (Green/Emerald Success) -->
                        <template x-if="confirmType === 'activate'">
                            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </template>

                        <h3 class="text-2xl font-bold text-gray-900 mb-4" x-text="confirmTitle"></h3>
                        <p class="text-[15px] text-gray-500 mb-2 leading-relaxed px-2" x-text="confirmText"></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-center gap-4 px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                        <button type="button" @click="showConfirmModal = false" class="w-full px-6 py-3.5 text-[15px] font-bold text-gray-700 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="confirmCallback()" 
                                :disabled="statusLoading"
                                :class="confirmType === 'suspend' ? 'bg-[#EF4444] hover:bg-red-600 shadow-[0_8px_20px_-6px_rgba(239,68,68,0.5)]' : 'bg-emerald-600 hover:bg-emerald-700 shadow-[0_8px_20px_-6px_rgba(5,150,105,0.5)]'"
                                class="w-full px-6 py-3.5 text-[15px] font-bold text-white rounded-2xl transition-all flex items-center justify-center gap-2">
                            
                            <template x-if="statusLoading">
                                <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            </template>
                            
                            <span x-text="confirmType === 'suspend' ? 'Ya, Suspend' : 'Ya, Aktifkan'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection
