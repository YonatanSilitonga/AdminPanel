@extends('admin.layouts.app')

@section('title', 'Log Audit')
@section('navbar_title', 'Log Audit')
@section('page_title', 'Log Audit')
@section('page_description', 'Rekam jejak seluruh aktivitas admin di sistem')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Pengaturan</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Log Audit</span>
</nav>
@endsection

@section('content')

<!-- Settings Navigation Tabs -->
@include('admin.settings.partials.tabs')

<div x-data="{
    showDetailModal: false,
    loading: false,
    detailLog: null,

    async openDetailModal(logId) {
        this.showDetailModal = true;
        this.loading = true;
        this.detailLog = null;
        try {
            const response = await fetch(`/admin/settings/audit-logs/${logId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            this.detailLog = await response.json();
        } catch (error) {
            console.error('Error loading audit log:', error);
            alert('Gagal memuat detail log: ' + error.message);
            this.showDetailModal = false;
        } finally {
            this.loading = false;
        }
    },

    getActionColor(action) {
        const colors = {
            // Generic CRUD
            'create':                          'bg-green-50 text-green-700',
            'update':                          'bg-blue-50 text-blue-700',
            'delete':                          'bg-red-50 text-red-700',
            'soft_delete':                     'bg-orange-50 text-orange-700',
            'restore':                         'bg-purple-50 text-purple-700',
            // Destination
            'create_mongo':                    'bg-green-50 text-green-700',
            'update_mongo':                    'bg-blue-50 text-blue-700',
            'delete_mongo':                    'bg-red-50 text-red-700',
            // Report
            'update_report_status_mongo':      'bg-blue-50 text-blue-700',
            'delete_report_mongo':             'bg-red-50 text-red-700',
            // Review / Sentiment
            'analyze_review_sentiment':        'bg-violet-50 text-violet-700',
            'batch_analyze_review_sentiment':  'bg-violet-50 text-violet-700',
            // Status & toggle
            'toggle_status':                   'bg-amber-50 text-amber-700',
            'update_status':                   'bg-amber-50 text-amber-700',
            'approve':                         'bg-emerald-50 text-emerald-700',
            'reject':                          'bg-pink-50 text-pink-700',
            // Settings
            'update_general_settings':         'bg-indigo-50 text-indigo-700',
            'update_settings':                 'bg-indigo-50 text-indigo-700',
            'update_api_keys':                 'bg-cyan-50 text-cyan-700',
            'update_ai_config':                'bg-violet-50 text-violet-700',
            // Maintenance
            'toggle_maintenance':              'bg-yellow-50 text-yellow-700',
        };
        // Fallback: detect by prefix
        if (!colors[action]) {
            if (action?.startsWith('create')) return 'bg-green-50 text-green-700';
            if (action?.startsWith('update')) return 'bg-blue-50 text-blue-700';
            if (action?.startsWith('delete')) return 'bg-red-50 text-red-700';
            if (action?.startsWith('analyze') || action?.startsWith('batch')) return 'bg-violet-50 text-violet-700';
        }
        return colors[action] || 'bg-gray-50 text-gray-700';
    },

    getActionLabel(action) {
        const labels = {
            // Generic CRUD
            'create':                          'Tambah Data',
            'update':                          'Ubah Data',
            'delete':                          'Hapus Data',
            'soft_delete':                     'Arsipkan Data',
            'restore':                         'Pulihkan Data',
            // Destination
            'create_mongo':                    'Tambah Destinasi',
            'update_mongo':                    'Ubah Destinasi',
            'delete_mongo':                    'Hapus Destinasi',
            // Report
            'update_report_status_mongo':      'Ubah Status Laporan',
            'delete_report_mongo':             'Hapus Laporan',
            // Review / Sentiment
            'analyze_review_sentiment':        'Analisis Sentimen Ulasan',
            'batch_analyze_review_sentiment':  'Analisis Sentimen Massal',
            // Status & toggle
            'toggle_status':                   'Ubah Status Aktif',
            'update_status':                   'Ubah Status',
            'approve':                         'Setujui',
            'reject':                          'Tolak',
            // Settings
            'update_general_settings':         'Ubah Pengaturan Umum',
            'update_settings':                 'Ubah Pengaturan',
            'update_api_keys':                 'Ubah API Keys',
            'update_ai_config':                'Ubah Konfigurasi AI',
            // Maintenance
            'toggle_maintenance':              'Toggle Mode Pemeliharaan',
        };
        // Fallback: humanize the raw action string
        return labels[action] || (action ? action.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : '-');
    }
}" x-cloak>


<!-- Stats Overview -->
<div class="bg-white rounded-[2rem] border border-gray-100 p-8 mb-8 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="flex items-center gap-4 px-6 first:pl-0">
            <div class="w-1 h-10 bg-sidebar rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($logs->total()) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Total Log</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah keseluruhan riwayat aktivitas admin yang terekam di sistem.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Statistik pemantauan sistem Log Audit untuk audit kepatuhan internal.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-green-500 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($logs->where('action', 'create')->count()) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Create</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah tindakan penambahan data atau objek baru oleh admin.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Statistik pemantauan sistem Log Audit.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-blue-500 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($logs->where('action', 'update')->count()) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Update</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah tindakan penyuntingan atau modifikasi data yang ada di sistem.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Statistik pemantauan sistem Log Audit.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8 last:pr-0">
            <div class="w-1 h-10 bg-red-500 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($logs->where('action', 'delete')->count() + $logs->where('action', 'soft_delete')->count()) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Delete</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah tindakan penghapusan data (permanen maupun soft-delete) dari sistem.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Statistik pemantauan sistem Log Audit.</p>
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
<div class="bg-white rounded-[2rem] border border-gray-100 p-8 mb-8 shadow-sm">
    <form method="GET" action="{{ route('admin.settings.audit-logs') }}" class="space-y-6" id="audit-filter-form">
        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

        <!-- Row 1: Search, Action, Module -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kata Kunci Search --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kata Kunci</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Mencari log aktivitas berdasarkan alamat IP admin atau ID entitas spesifik.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                    <p class="text-slate-200 font-normal">Query pemfilteran data log di basis data.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="IP address atau Entity ID..."
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/20 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-400 font-medium transition-all">
                </div>
            </div>

            {{-- Action Filter --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tindakan</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Memfilter log audit berdasarkan jenis aksi (tindakan) yang terjadi.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                    <p class="text-slate-200 font-normal">Pemfilteran pencarian log audit.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <select name="action" onchange="this.form.submit()"
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:border-sidebar transition-all focus:ring-2 focus:ring-sidebar/20 focus:border-sidebar appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236B7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5em] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                    <option value="">Semua Tindakan</option>
                    @php
                        $filterActionLabels = [
                            'create'                          => 'Tambah Data',
                            'update'                          => 'Ubah Data',
                            'delete'                          => 'Hapus Data',
                            'soft_delete'                     => 'Arsipkan Data',
                            'restore'                         => 'Pulihkan Data',
                            'create_mongo'                    => 'Tambah Destinasi',
                            'update_mongo'                    => 'Ubah Destinasi',
                            'delete_mongo'                    => 'Hapus Destinasi',
                            'update_report_status_mongo'      => 'Ubah Status Laporan',
                            'delete_report_mongo'             => 'Hapus Laporan',
                            'analyze_review_sentiment'        => 'Analisis Sentimen Ulasan',
                            'batch_analyze_review_sentiment'  => 'Analisis Sentimen Massal',
                            'toggle_status'                   => 'Ubah Status Aktif',
                            'update_status'                   => 'Ubah Status',
                            'approve'                         => 'Setujui',
                            'reject'                          => 'Tolak',
                            'update_general_settings'         => 'Ubah Pengaturan Umum',
                            'update_settings'                 => 'Ubah Pengaturan',
                            'update_api_keys'                 => 'Ubah API Keys',
                            'update_ai_config'                => 'Ubah Konfigurasi AI',
                            'toggle_maintenance'              => 'Toggle Mode Pemeliharaan',
                        ];
                    @endphp
                    @foreach($actions->filter(fn($a) => !empty($a)) as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>
                            {{ $filterActionLabels[$action] ?? ucwords(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Module Filter --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Modul</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Memfilter log audit berdasarkan modul sistem yang dimanipulasi.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                    <p class="text-slate-200 font-normal">Pemfilteran pencarian log audit.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <select name="module" onchange="this.form.submit()"
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:border-sidebar transition-all focus:ring-2 focus:ring-sidebar/20 focus:border-sidebar appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236B7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5em] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                    <option value="">Semua Modul</option>
                    @foreach($entityTypes->filter(fn($t) => !empty($t)) as $type)
                        <option value="{{ $type }}" @selected(request('module') === $type)>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 2: Period, Display, Reset -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-2">

            {{-- Periode Waktu --}}
            <div class="lg:col-span-7 flex flex-col gap-2" x-data="{ 
                dateRange: '{{ request('date_range', '') }}',
                showCustom: {{ request('date_range') === 'custom' ? 'true' : 'false' }}
            }">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Periode Waktu</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Membatasi penayangan log berdasarkan periode waktu tertentu dengan pilihan preset atau rentang kustom.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                    <p class="text-slate-200 font-normal">Pemfilteran pencarian log audit.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <div class="grid lg:grid-cols-3 gap-3">
                    <select name="date_range" 
                        x-model="dateRange"
                        @change="showCustom = ($event.target.value === 'custom'); if ($event.target.value !== 'custom') $el.form.submit();"
                        class="lg:col-span-1 w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:border-sidebar transition-all focus:ring-2 focus:ring-sidebar/20 focus:border-sidebar appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236B7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5em] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                        <option value="">Semua Waktu</option>
                        <option value="today" @selected(request('date_range') === 'today')>Hari Ini</option>
                        <option value="yesterday" @selected(request('date_range') === 'yesterday')>Kemarin</option>
                        <option value="last_7_days" @selected(request('date_range') === 'last_7_days')>7 Hari Terakhir</option>
                        <option value="last_30_days" @selected(request('date_range') === 'last_30_days')>30 Hari Terakhir</option>
                        <option value="this_month" @selected(request('date_range') === 'this_month')>Bulan Ini</option>
                        <option value="last_month" @selected(request('date_range') === 'last_month')>Bulan Lalu</option>
                        <option value="custom" @selected(request('date_range') === 'custom')>Rentang Kustom</option>
                    </select>
                    
                    <div x-show="showCustom" x-transition class="lg:col-span-2 grid grid-cols-2 gap-3">
                        <input type="date" name="custom_date_from" value="{{ request('custom_date_from') }}" 
                            placeholder="Dari Tanggal"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-500 font-medium focus:ring-2 focus:ring-sidebar/20 focus:border-sidebar transition-all">
                        <input type="date" name="custom_date_to" value="{{ request('custom_date_to') }}" 
                            placeholder="Sampai Tanggal"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-500 font-medium focus:ring-2 focus:ring-sidebar/20 focus:border-sidebar transition-all">
                    </div>
                </div>
            </div>

            {{-- Tampilkan --}}
            <div class="lg:col-span-3 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tampilkan</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menentukan batasan jumlah baris data log yang akan ditampilkan pada satu halaman.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                    <p class="text-slate-200 font-normal">Sistem paginasi tabel Log Audit.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <select name="per_page" onchange="this.form.submit()"
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:border-sidebar transition-all focus:ring-2 focus:ring-sidebar/20 focus:border-sidebar appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236B7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5em] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                    @foreach([10, 25, 50, 100] as $val)
                        <option value="{{ $val }}" @selected(request('per_page', 25) == $val)>{{ $val }} Baris</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Aksi --}}
            <div class="lg:col-span-2 flex flex-col gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest opacity-0 pointer-events-none">Aksi</span>
                <div class="flex gap-3">
                    @if(request()->anyFilled(['search', 'action', 'module', 'date_range', 'custom_date_from', 'custom_date_to']))
                        <a href="{{ route('admin.settings.audit-logs') }}"
                            class="flex-1 text-center py-3 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-2xl text-sm transition-all border border-red-200 shadow-sm flex items-center justify-center gap-2 hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reset
                        </a>
                    @endif
                    <button type="submit" x-show="$el.form.date_range.value === 'custom'" x-cloak
                        class="flex-1 py-3 bg-sidebar hover:bg-sidebar-dark text-white font-bold rounded-2xl text-sm transition-all shadow-sm flex items-center justify-center gap-2 hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Audit Logs Table -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-50">
            <thead class="bg-white">
                @php
                    $currentSort = request('sort_by', 'created_at');
                    $sortOrder   = request('sort_order', 'desc') === 'asc' ? 'desc' : 'asc';
                @endphp
                <tr>
                    <th class="px-10 py-6 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($currentSort === 'created_at' ? $sortOrder : 'asc')]) }}"
                           class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-sidebar transition-colors">
                            Waktu
                            <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'text-sidebar' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'created_at' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                            </svg>
                        </a>
                    </th>
                    <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Admin</th>
                    <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Entity</th>
                    <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">IP Address</th>
                    <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($logs as $log)
                    @php
                        $actionColors = [
                            'create'                          => 'bg-green-50 text-green-700',
                            'update'                          => 'bg-blue-50 text-blue-700',
                            'delete'                          => 'bg-red-50 text-red-700',
                            'soft_delete'                     => 'bg-orange-50 text-orange-700',
                            'restore'                         => 'bg-purple-50 text-purple-700',
                            'create_mongo'                    => 'bg-green-50 text-green-700',
                            'update_mongo'                    => 'bg-blue-50 text-blue-700',
                            'delete_mongo'                    => 'bg-red-50 text-red-700',
                            'update_report_status_mongo'      => 'bg-blue-50 text-blue-700',
                            'delete_report_mongo'             => 'bg-red-50 text-red-700',
                            'analyze_review_sentiment'        => 'bg-violet-50 text-violet-700',
                            'batch_analyze_review_sentiment'  => 'bg-violet-50 text-violet-700',
                            'toggle_status'                   => 'bg-amber-50 text-amber-700',
                            'update_status'                   => 'bg-amber-50 text-amber-700',
                            'approve'                         => 'bg-emerald-50 text-emerald-700',
                            'reject'                          => 'bg-pink-50 text-pink-700',
                            'update_general_settings'         => 'bg-indigo-50 text-indigo-700',
                            'update_settings'                 => 'bg-indigo-50 text-indigo-700',
                            'update_api_keys'                 => 'bg-cyan-50 text-cyan-700',
                            'update_ai_config'                => 'bg-violet-50 text-violet-700',
                            'toggle_maintenance'              => 'bg-yellow-50 text-yellow-700',
                        ];
                        $actionLabels = [
                            'create'                          => 'Tambah Data',
                            'update'                          => 'Ubah Data',
                            'delete'                          => 'Hapus Data',
                            'soft_delete'                     => 'Arsipkan Data',
                            'restore'                         => 'Pulihkan Data',
                            'create_mongo'                    => 'Tambah Destinasi',
                            'update_mongo'                    => 'Ubah Destinasi',
                            'delete_mongo'                    => 'Hapus Destinasi',
                            'update_report_status_mongo'      => 'Ubah Status Laporan',
                            'delete_report_mongo'             => 'Hapus Laporan',
                            'analyze_review_sentiment'        => 'Analisis Sentimen Ulasan',
                            'batch_analyze_review_sentiment'  => 'Analisis Sentimen Massal',
                            'toggle_status'                   => 'Ubah Status Aktif',
                            'update_status'                   => 'Ubah Status',
                            'approve'                         => 'Setujui',
                            'reject'                          => 'Tolak',
                            'update_general_settings'         => 'Ubah Pengaturan Umum',
                            'update_settings'                 => 'Ubah Pengaturan',
                            'update_api_keys'                 => 'Ubah API Keys',
                            'update_ai_config'                => 'Ubah Konfigurasi AI',
                            'toggle_maintenance'              => 'Toggle Mode Pemeliharaan',
                        ];
                        // Fallback color by prefix
                        $rawAction = $log->action ?? '';
                        if (isset($actionColors[$rawAction])) {
                            $color = $actionColors[$rawAction];
                        } elseif (str_starts_with($rawAction, 'create')) {
                            $color = 'bg-green-50 text-green-700';
                        } elseif (str_starts_with($rawAction, 'update')) {
                            $color = 'bg-blue-50 text-blue-700';
                        } elseif (str_starts_with($rawAction, 'delete')) {
                            $color = 'bg-red-50 text-red-700';
                        } elseif (str_starts_with($rawAction, 'analyze') || str_starts_with($rawAction, 'batch')) {
                            $color = 'bg-violet-50 text-violet-700';
                        } else {
                            $color = 'bg-gray-50 text-gray-700';
                        }
                        $label = $actionLabels[$rawAction] ?? ucwords(str_replace('_', ' ', $rawAction));
                    @endphp
                    <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                        <td class="px-10 py-6">
                            <div class="text-sm font-medium text-gray-700">{{ $log->created_at->format('d M Y H:i:s') }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-sidebar/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-sidebar">{{ strtoupper(substr($log->admin?->name ?? '?', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $log->admin?->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->admin?->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1.5 {{ $color }} rounded-lg text-[11px] font-bold uppercase tracking-wider">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-10 py-6">
                            <div class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $log->entity_type)) }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $log->entity_id ? '#' . substr($log->entity_id, -8) : '-' }}</div>
                        </td>
                        <td class="px-10 py-6">
                            <span class="font-mono text-sm text-gray-600">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <button @click="openDetailModal('{{ $log->id }}')"
                                class="p-2.5 bg-sidebar/5 text-sidebar rounded-full hover:bg-sidebar/10 transition-all" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm font-bold">Tidak ada log audit yang ditemukan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($logs) && method_exists($logs, 'links'))
    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between bg-white">
        <div class="text-gray-400 text-sm font-medium">Menampilkan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} Log</div>
        <div>{{ $logs->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
    </div>
    @endif
</div>

<!-- Detail Modal -->
<div x-show="showDetailModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="showDetailModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showDetailModal = false"></div>

        <div x-show="showDetailModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-8 pt-7 pb-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-sidebar/10 rounded-xl flex items-center justify-center text-sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Aktivitas</h3>
                        <p class="text-xs text-gray-400 font-medium">Informasi lengkap perubahan yang dilakukan</p>
                    </div>
                </div>
                <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Loading -->
            <div x-show="loading && !detailLog" class="py-16 flex justify-center">
                <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>

            <!-- Content -->
            <div x-show="detailLog" class="px-8 py-6 space-y-5">
                <!-- Admin & Timestamp -->
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                    <div class="w-11 h-11 bg-sidebar/10 rounded-full flex items-center justify-center text-sidebar font-bold text-base flex-shrink-0">
                        <span x-text="detailLog?.admin?.name?.charAt(0)?.toUpperCase() || 'A'"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 text-sm truncate" x-text="detailLog?.admin?.name ?? 'Unknown'"></p>
                        <p class="text-xs text-gray-400 truncate" x-text="detailLog?.admin?.email ?? '-'"></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Waktu</p>
                        <p class="text-xs font-bold text-gray-700 mt-0.5" x-text="detailLog?.created_at ? new Date(detailLog.created_at).toLocaleString('id-ID') : '-'"></p>
                    </div>
                </div>

                <!-- IP Address -->
                <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                    <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1.5">IP Address</p>
                    <p class="text-sm font-bold text-blue-900 font-mono" x-text="detailLog?.ip_address ?? '-'"></p>
                </div>

                <!-- Action & Entity -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Action</p>
                        <span class="inline-block px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider"
                              :class="getActionColor(detailLog?.action)"
                              x-text="getActionLabel(detailLog?.action)"></span>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Entity Type</p>
                        <p class="text-sm font-bold text-gray-800" x-text="detailLog?.entity_type ? detailLog.entity_type.replace(/_/g, ' ') : '-'"></p>
                    </div>
                </div>

                <!-- Entity ID -->
                <template x-if="detailLog?.entity_id">
                    <div class="p-4 bg-gray-50 rounded-2xl flex flex-col gap-1.5">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Target Entity</p>
                        
                        <!-- friendly resolved name -->
                        <template x-if="detailLog?.resolved_entity_name">
                            <p class="text-sm font-bold text-gray-800" x-text="detailLog.resolved_entity_name"></p>
                        </template>
                        
                        <!-- raw entity ID -->
                        <p class="text-xs font-mono text-gray-400 font-bold" x-text="'ID: ' + detailLog.entity_id"></p>
                        
                        <!-- action link -->
                        <template x-if="detailLog?.resolved_entity_url">
                            <div class="mt-1">
                                <a :href="detailLog.resolved_entity_url" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-sidebar text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-sidebar/10 hover:shadow-md hover:bg-opacity-90">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Buka Halaman Detail
                                </a>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Old Values -->
                <template x-if="detailLog?.old_values">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Nilai Lama</p>
                        <pre class="bg-red-50 border border-red-100 p-4 rounded-2xl text-xs text-gray-700 overflow-auto max-h-48 font-mono leading-relaxed whitespace-pre-wrap"
                             x-text="JSON.stringify(detailLog.old_values, null, 2)"></pre>
                    </div>
                </template>

                <!-- New Values -->
                <template x-if="detailLog?.new_values">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Nilai Baru</p>
                        <pre class="bg-green-50 border border-green-100 p-4 rounded-2xl text-xs text-gray-700 overflow-auto max-h-48 font-mono leading-relaxed whitespace-pre-wrap"
                             x-text="JSON.stringify(detailLog.new_values, null, 2)"></pre>
                    </div>
                </template>

                <!-- Close -->
                <div class="flex justify-end pt-2">
                    <button @click="showDetailModal = false"
                        class="px-8 py-3 text-sm font-bold text-gray-500 border border-gray-200 rounded-xl hover:text-gray-700 hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection
