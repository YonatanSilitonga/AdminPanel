@extends('admin.layouts.app')

@section('title', 'Log Audit')
@section('navbar_title', 'Log Audit')
@section('page_title', 'Log Audit')
@section('page_description', 'Rekam jejak seluruh aktivitas admin di sistem')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
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
            'create': 'bg-green-50 text-green-700',
            'update': 'bg-blue-50 text-blue-700',
            'delete': 'bg-red-50 text-red-700',
            'soft_delete': 'bg-orange-50 text-orange-700',
            'restore': 'bg-purple-50 text-purple-700',
            'approve': 'bg-emerald-50 text-emerald-700',
            'reject': 'bg-pink-50 text-pink-700',
            'toggle_maintenance': 'bg-yellow-50 text-yellow-700',
            'update_settings': 'bg-indigo-50 text-indigo-700',
            'update_api_keys': 'bg-cyan-50 text-cyan-700',
            'update_ai_config': 'bg-violet-50 text-violet-700',
        };
        return colors[action] || 'bg-gray-50 text-gray-700';
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
<div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
    <form method="GET" action="{{ route('admin.settings.audit-logs') }}" class="space-y-4" id="audit-filter-form">
        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {{-- Kata Kunci --}}
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5">
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
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="IP address atau Entity ID..."
                        class="w-full pl-12 pr-4 py-2.5 bg-white border border-gray-100 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-300 font-medium">
                </div>
            </div>

            {{-- Admin Filter --}}
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Admin</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan aktivitas yang hanya dilakukan oleh administrator tertentu.</p>
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
                <select name="admin_id" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-sidebar/10">
                    <option value="">Semua Admin</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" @selected(request('admin_id') == $admin->id)>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Action Filter --}}
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tindakan</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans font-sans">
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
                    class="w-full px-4 py-2.5 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-sidebar/10">
                    <option value="">Semua Tindakan</option>
                    @foreach($actions->filter(fn($a) => !empty($a)) as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>
                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Entity Type Filter --}}
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Entitas</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Memfilter log audit berdasarkan tipe data/model entitas yang dimanipulasi.</p>
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
                <select name="entity_type" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-sidebar/10">
                    <option value="">Semua Entitas</option>
                    @foreach($entityTypes->filter(fn($t) => !empty($t)) as $type)
                        <option value="{{ $type }}" @selected(request('entity_type') === $type)>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Rentang Tanggal --}}
            <div class="flex flex-col gap-1.5 sm:col-span-2">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Rentang Tanggal</span>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Membatasi penayangan log hanya dalam batas awal (dari) dan batas akhir (hingga) tanggal tertentu.</p>
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
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()"
                        class="w-full px-4 py-2 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-500 font-medium focus:ring-2 focus:ring-sidebar/10">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"
                        class="w-full px-4 py-2 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-500 font-medium focus:ring-2 focus:ring-sidebar/10">
                </div>
            </div>

            {{-- Tampilkan --}}
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5">
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
                    class="w-full px-4 py-2.5 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-bold focus:ring-2 focus:ring-sidebar/10 cursor-pointer">
                    @foreach([10, 25, 50, 100] as $val)
                        <option value="{{ $val }}" @selected(request('per_page', 25) == $val)>{{ $val }} Baris</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Button --}}
            <div class="flex items-end">
                @if(request()->anyFilled(['search', 'admin_id', 'action', 'entity_type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.settings.audit-logs') }}"
                        class="w-full text-center py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-2xl text-sm transition-colors border border-red-100 shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Reset Filter
                    </a>
                @endif
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
                            'create'             => 'bg-green-50 text-green-700',
                            'update'             => 'bg-blue-50 text-blue-700',
                            'delete'             => 'bg-red-50 text-red-700',
                            'soft_delete'        => 'bg-orange-50 text-orange-700',
                            'restore'            => 'bg-purple-50 text-purple-700',
                            'approve'            => 'bg-emerald-50 text-emerald-700',
                            'reject'             => 'bg-pink-50 text-pink-700',
                            'toggle_maintenance' => 'bg-yellow-50 text-yellow-700',
                            'update_settings'    => 'bg-indigo-50 text-indigo-700',
                            'update_api_keys'    => 'bg-cyan-50 text-cyan-700',
                            'update_ai_config'   => 'bg-violet-50 text-violet-700',
                        ];
                        $color = $actionColors[$log->action] ?? 'bg-gray-50 text-gray-700';
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
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
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
    @if($logs->hasPages())
    <div class="px-8 py-6 border-t border-gray-50 flex items-center justify-between bg-white">
        <p class="text-[13px] text-gray-400 font-medium">
            Menampilkan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} log
        </p>
        <div class="flex items-center gap-2">
            @if($logs->onFirstPage())
                <span class="px-4 py-2 text-[13px] font-bold text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">Prev</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="px-4 py-2 text-[13px] font-bold text-gray-600 bg-gray-100 hover:bg-sidebar hover:text-white rounded-lg transition-all">Prev</a>
            @endif

            <div class="flex items-center gap-1">
                @foreach($logs->getUrlRange(max(1, $logs->currentPage()-1), min($logs->lastPage(), $logs->currentPage()+1)) as $page => $url)
                    <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center text-[13px] font-bold {{ $page == $logs->currentPage() ? 'bg-sidebar text-white shadow-lg shadow-sidebar/30' : 'text-gray-500 hover:bg-gray-100' }} rounded-lg transition-all">{{ $page }}</a>
                @endforeach
            </div>

            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="px-4 py-2 text-[13px] font-bold text-gray-600 bg-gray-100 hover:bg-sidebar hover:text-white rounded-lg transition-all">Next</a>
            @else
                <span class="px-4 py-2 text-[13px] font-bold text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">Next</span>
            @endif
        </div>
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
                              x-text="detailLog?.action ? detailLog.action.replace(/_/g, ' ') : '-'"></span>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Entity Type</p>
                        <p class="text-sm font-bold text-gray-800" x-text="detailLog?.entity_type ? detailLog.entity_type.replace(/_/g, ' ') : '-'"></p>
                    </div>
                </div>

                <!-- Entity ID -->
                <template x-if="detailLog?.entity_id">
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Entity ID</p>
                        <p class="text-sm font-mono font-bold text-gray-700" x-text="detailLog?.entity_id"></p>
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
