@extends('admin.layouts.app')

@push('styles')
<style>
    /* Fix Google Autocomplete Suggestions in Modals */
    .pac-container {
        z-index: 9999 !important;
        border-radius: 1rem;
        border: none;
        margin-top: 5px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        font-family: inherit;
    }
    .pac-item {
        padding: 10px 15px;
        cursor: pointer;
        border-top: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pac-item:first-child { border-top: none; }
    .pac-item:hover { background-color: #f9fafb; }
    .pac-item-query { font-size: 14px; color: #374151; font-weight: 600; }
    .pac-matched { color: #066466; }
    .pac-icon { display: none; }
</style>
@endpush

@section('navbar_title', 'Fasilitas Umum')
@section('title', 'Kelola Fasilitas Umum')
@section('page_title', 'Fasilitas Umum')
@section('page_description', 'Kelola data fasilitas umum di kawasan Danau Toba')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Fasilitas Umum</span>
</nav>
@endsection

@section('page_actions')
<div class="flex items-center gap-3">
    <button type="button" x-data x-on:click="$dispatch('open-create-modal')" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        Tambah Fasilitas
    </button>
    <div class="relative group cursor-pointer inline-flex items-center">
        <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
            <div class="space-y-2">
                <div>
                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Tambah Fasilitas</span>
                    <p class="text-slate-200 font-sans leading-relaxed">Membuka formulir penambahan sarana prasarana umum baru di sekitar Toba (seperti toilet, ATM, tempat ibadah, pusat informasi, dll.) beserta ikon petanya.</p>
                </div>
            </div>
            <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="facility-manager" x-data="facilityManager()" @open-create-modal.window="showCreateModal = true">
    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true" @open-create-modal.window="showCreateModal = true"></button>
    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
        <form method="GET" action="{{ route('admin.fasilitas_umum.index') }}" class="space-y-4">
            <!-- Hidden inputs for sorting persistence -->
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kata Kunci -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Kata Kunci
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Menyaring daftar fasilitas umum berdasarkan kecocokan nama, alamat, atau jenis.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                        <p class="text-slate-200 font-normal">Pencarian cepat fasilitas di Panel Admin.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, alamat, jenis..."
                            class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm transition-all shadow-sm placeholder-gray-300">
                    </div>
                </div>

                <!-- Jenis -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Jenis
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Menyaring fasilitas umum berdasarkan jenisnya (SPBU, Hotel, Resto, RS/Puskesmas, ATM).</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Menu pencarian fasilitas pada aplikasi mobile.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <select name="type" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-sidebar transition-all cursor-pointer">
                        <option value="">Semua Jenis</option>
                        <option value="SPBU" @selected(request('type') === 'SPBU')>SPBU</option>
                        <option value="Hotel" @selected(request('type') === 'Hotel')>Hotel</option>
                        <option value="Resto" @selected(request('type') === 'Resto')>Resto</option>
                        <option value="RS/Puskesmas" @selected(request('type') === 'RS/Puskesmas')>RS/Puskesmas</option>
                        <option value="ATM" @selected(request('type') === 'ATM')>ATM</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Status
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Menyaring fasilitas umum berdasarkan status keaktifan publikasinya.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Aplikasi mobile (hanya yang berstatus Aktif yang akan ditampilkan).</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-sidebar transition-all cursor-pointer">
                        <option value="all">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                    </select>
                </div>

                <!-- Tampilkan -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Tampilkan
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Menentukan jumlah baris data fasilitas umum yang ditampilkan dalam satu halaman.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                        <p class="text-slate-200 font-normal">Pagination tabel fasilitas umum di Panel Admin.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <div class="flex items-center gap-2">
                        <select name="per_page" onchange="this.form.submit()" class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm font-bold text-gray-700 shadow-sm hover:border-sidebar transition-all cursor-pointer">
                            @foreach([10, 15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }} Baris</option>
                            @endforeach
                        </select>
                        @if(request('search') || request('type') || (request('status') && request('status') !== 'all') || request('per_page') != 15)
                            <a href="{{ route('admin.fasilitas_umum.index') }}" class="px-4 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    @php
                        $currentSort = request('sort_by', 'created_at');
                        $sortOrder = request('sort_order', 'desc') === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($currentSort === 'name' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Fasilitas
                                <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'name' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'type', 'sort_order' => ($currentSort === 'type' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Jenis
                                <svg class="w-4 h-4 {{ $currentSort === 'type' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'type' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-400 uppercase tracking-wider">Alamat</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-400 uppercase tracking-wider">Jam Buka</th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_active', 'sort_order' => ($currentSort === 'is_active' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Status
                                <svg class="w-4 h-4 {{ $currentSort === 'is_active' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'is_active' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($facilities as $index => $facility)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-8 py-5 text-sm font-semibold text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    @if($facility->image_url)
                                        <img src="{{ image_url($facility->image_url) }}" alt="{{ $facility->name }}" @click="lightboxImage = '{{ image_url($facility->image_url) }}'; showLightbox = true" class="w-20 h-14 object-cover rounded-xl shadow-sm border border-gray-100 flex-shrink-0 cursor-pointer hover:scale-105 transition-transform" title="Klik untuk memperbesar">
                                    @else
                                        <div class="w-20 h-14 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="text-[14px] font-bold text-gray-800 max-w-[180px] truncate" title="{{ $facility->name }}">{{ $facility->name }}</div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                @php
                                    $typeColors = [
                                        'SPBU' => 'bg-orange-50 text-orange-500',
                                        'Hotel' => 'bg-teal-50 text-teal-600',
                                        'Resto' => 'bg-blue-50 text-blue-500',
                                        'RS/Puskesmas' => 'bg-green-50 text-green-600',
                                        'ATM' => 'bg-purple-50 text-purple-600',
                                    ];
                                    $colorClass = $typeColors[$facility->type] ?? 'bg-gray-50 text-gray-500';
                                @endphp
                                <span class="px-4 py-1.5 rounded-xl font-bold text-[11px] uppercase tracking-wider {{ $colorClass }}">
                                    {{ $facility->type }}
                                </span>
                            </td>
                            <td class="px-10 py-6">
                                <div class="text-[13px] text-gray-500 font-medium max-w-xs truncate">{{ $facility->address }}</div>
                            </td>
                            <td class="px-10 py-6">
                                <div class="text-[13px] text-gray-600 font-bold">{{ $facility->operational_hours }}</div>
                            </td>
                            <td class="px-10 py-6">
                                @if($facility->is_active)
                                    <span class="px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] text-xs font-bold rounded-xl">Aktif</span>
                                @else
                                    <span class="px-4 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold rounded-xl">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openViewModal('{{ (string)$facility->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button @click="openEditModal('{{ (string)$facility->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete-modal', { action: '{{ route('admin.fasilitas_umum.destroy', $facility->_id) }}', title: 'Hapus Fasilitas', type: 'fasilitas', name: '{{ $facility->name }}' })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-10 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <p class="text-gray-400 font-medium">Belum ada data fasilitas umum</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($facilities->hasPages())
        <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
            <div class="text-gray-400 text-sm font-medium">
                Menampilkan {{ $facilities->firstItem() }} - {{ $facilities->lastItem() }} dari {{ $facilities->total() }} data
            </div>
            <div>
                {{ $facilities->links('vendor.pagination.tailwind-custom') }}
            </div>
        </div>
        @endif
    </div>

    <!-- Create Modal -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showCreateModal = false"></div>

              <template x-if="showCreateModal">
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                  
                  <div class="flex items-center justify-between mb-8">
                      <div class="flex items-center gap-2">
                          <h3 class="text-xl font-bold text-gray-900">Tambah Fasilitas Umum</h3>
                          <div class="relative group cursor-pointer inline-flex items-center">
                              <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                              <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                  <div class="space-y-2">
                                      <div>
                                          <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Tambah Fasilitas</span>
                                          <p class="text-slate-200 font-normal">Formulir untuk mendaftarkan fasilitas umum baru di sekitar Danau Toba. Data lokasi dan jam operasional akan terintegrasi langsung dengan peta pencarian wisatawan.</p>
                                      </div>
                                  </div>
                                  <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                              </div>
                          </div>
                      </div>
                      <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      </button>
                  </div>
                
                <form id="createFacilityForm" @submit.prevent="submitCreate()" class="space-y-6">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Nama Fasilitas</label>
                        <input type="text" name="name" required placeholder="Contoh: SPBU Balige Utara" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Jenis Fasilitas</label>
                        <select name="type" required class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold appearance-none bg-no-repeat bg-[right_1.5rem_center] bg-[length:1.2em_1.2em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%239CA3AF%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')">
                            <option value="" disabled selected>Pilih jenis</option>
                            <option value="SPBU">SPBU</option>
                            <option value="Hotel">Hotel</option>
                            <option value="Resto">Resto</option>
                            <option value="RS/Puskesmas">RS/Puskesmas</option>
                            <option value="ATM">ATM</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Alamat</label>
                        <textarea name="address" required placeholder="Contoh: Jl. Sisingamangaraja No. 12" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300 min-h-[100px]"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Latitude</label>
                            <input type="text" name="latitude" id="create_latitude" required placeholder="Contoh: 2.3361" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300" readonly>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Longitude</label>
                            <input type="text" name="longitude" id="create_longitude" required placeholder="Contoh: 99.0494" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300" readonly>
                        </div>
                    </div>

                    {{-- Map Picker for Create --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Pilih Lokasi di Peta</label>
                        <div class="flex gap-2 mb-2">
                            <div class="relative flex-1 group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" id="create_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-bold placeholder-gray-300 transition-all" autocomplete="off">
                                <button type="button" onclick="performSearch('create_location_search', 'create_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-xl hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </div>
                            <button type="button" onclick="getCurrentLocation('create_latitude', 'create_longitude', 'create_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-2xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </button>
                        </div>
                        <div id="create_map_picker" style="width: 100%; height: 250px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                        <p class="text-[10px] text-gray-400 italic mt-1">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Jam Operasional</label>
                            <input type="text" name="operational_hours" required placeholder="Contoh: 06.00-22.00" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Nomor Telepon</label>
                            <input type="text" name="phone_number" placeholder="Contoh: +62 812 3456 7890" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Deskripsi Fasilitas</label>
                        <textarea name="description" placeholder="Ceritakan tentang fasilitas ini..." class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300 min-h-[100px]"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Layanan Tersedia</label>
                        <input type="text" name="available_services" placeholder="Pisahkan dengan koma. Contoh: Free Wi-Fi, Kolam Renang, Parkir" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Tags / Label</label>
                        <input type="text" name="tags" placeholder="Pisahkan dengan koma. Contoh: Pemandangan Danau, Ramah Anak" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                    </div>

                    <!-- Panduan Foto Fasilitas -->
                    <div class="bg-emerald-50/50 border border-emerald-100/80 rounded-2xl p-4 text-xs text-gray-600 space-y-2">
                        <div class="flex items-center gap-2 text-[#066466] font-bold">
                            <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Panduan Foto Fasilitas Umum</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <div class="space-y-1">
                                <span class="font-bold text-gray-700 block">1. Foto Utama (Thumbnail / Cover)</span>
                                <p class="leading-relaxed">Akan digunakan sebagai <strong>sampul utama</strong> pada daftar fasilitas umum di aplikasi mobile.</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-bold text-gray-700 block">2. Foto Tambahan (Galeri)</span>
                                <p class="leading-relaxed">Akan ditampilkan sebagai <strong>galeri gambar tambahan</strong> di halaman detail fasilitas umum.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Foto Utama (Cover) -->
                        <div class="space-y-2" x-data="{ coverPreview: '' }">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Foto Utama (Cover)</label>
                            <div class="relative group">
                                <input type="file" name="image" id="create_image" class="hidden" 
                                    @change="
                                        createFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                        if ($event.target.files[0]) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => { coverPreview = e.target.result; };
                                            reader.readAsDataURL($event.target.files[0]);
                                        } else {
                                            coverPreview = '';
                                        }
                                    ">
                                <label for="create_image" class="relative flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                    <template x-if="coverPreview">
                                        <div class="absolute inset-0 w-full h-full bg-gray-100">
                                            <img :src="coverPreview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                <p class="text-white text-xs font-bold">Ganti Foto Utama</p>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!coverPreview">
                                        <div class="flex flex-col items-center justify-center text-center px-4">
                                            <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Pilih foto utama'"></p>
                                            <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP (Maks. 5MB)</p>
                                        </div>
                                    </template>
                                </label>
                            </div>
                        </div>

                        <!-- Foto Tambahan (Galeri) -->
                        <div class="space-y-2" x-data="{ galleryPreviews: [] }">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Foto Tambahan (Galeri)</label>
                            <div class="relative group">
                                <input type="file" name="images[]" id="create_images" multiple class="hidden" 
                                    @change="
                                        galleryPreviews = [];
                                        const files = $event.target.files;
                                        for (let i = 0; i < files.length; i++) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => { galleryPreviews.push(e.target.result); };
                                            reader.readAsDataURL(files[i]);
                                        }
                                    ">
                                <label for="create_images" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                    <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700" x-text="galleryPreviews.length > 0 ? galleryPreviews.length + ' file dipilih' : 'Pilih foto tambahan'"></p>
                                    <p class="text-[10px] text-gray-400 mt-1">Bisa pilih lebih dari 1</p>
                                </label>
                            </div>
                            
                            <!-- Previews -->
                            <template x-if="galleryPreviews.length > 0">
                                <div class="grid grid-cols-4 gap-2 mt-2">
                                    <template x-for="(src, idx) in galleryPreviews" :key="idx">
                                        <div class="relative rounded-xl overflow-hidden aspect-square border border-gray-200">
                                            <img :src="src" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-50 rounded-2xl flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">Status Aktif</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Aktifkan untuk menampilkan di aplikasi</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" x-model="createIsActive" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateModal = false" class="px-8 py-4 text-sm font-bold text-gray-400 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all">Batal</button>
                        <button type="submit" class="px-10 py-4 bg-sidebar text-white text-sm font-bold rounded-2xl shadow-lg shadow-sidebar/20 hover:opacity-95 transition-all flex items-center gap-2" :disabled="loading">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Simpan Fasilitas</span>
                        </button>
                    </div>
                </form>
              </div>
            </template>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showEditModal = false"></div>

              <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                  class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Edit Fasilitas Umum</h3>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Edit Fasilitas</span>
                                        <p class="text-slate-200 font-normal">Memperbarui data fasilitas umum seperti nama, alamat, titik koordinat, jam operasional, atau mengelola galeri foto.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !editingFacility" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div x-show="editingFacility" class="w-full">
                    <form id="editFacilityForm" @submit.prevent="submitUpdate()" class="space-y-6">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Nama Fasilitas</label>
                        <input type="text" name="name" x-model="editingFacility.name" required class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Jenis Fasilitas</label>
                        <select name="type" x-model="editingFacility.type" required class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold appearance-none bg-no-repeat bg-[right_1.5rem_center] bg-[length:1.2em_1.2em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%239CA3AF%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')">
                            <option value="SPBU">SPBU</option>
                            <option value="Hotel">Hotel</option>
                            <option value="Resto">Resto</option>
                            <option value="RS/Puskesmas">RS/Puskesmas</option>
                            <option value="ATM">ATM</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Alamat</label>
                        <textarea name="address" x-model="editingFacility.address" required class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold min-h-[100px]"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Latitude</label>
                            <input type="text" name="latitude" id="edit_latitude" x-model="editingFacility.latitude" placeholder="Contoh: 2.3361" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300" readonly>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Longitude</label>
                            <input type="text" name="longitude" id="edit_longitude" x-model="editingFacility.longitude" placeholder="Contoh: 99.0494" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300" readonly>
                        </div>
                    </div>

                    {{-- Map Picker for Edit --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Lokasi Fasilitas (Klik/Geser untuk mengubah)</label>
                        <div class="flex gap-2 mb-2">
                            <div class="relative flex-1 group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" id="edit_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-bold placeholder-gray-300 transition-all" autocomplete="off">
                                <button type="button" onclick="performSearch('edit_location_search', 'edit_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-xl hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </div>
                            <button type="button" onclick="getCurrentLocation('edit_latitude', 'edit_longitude', 'edit_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-2xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </button>
                        </div>
                        <div id="edit_map_picker" style="width: 100%; height: 250px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                        <p class="text-[10px] text-gray-400 italic mt-1">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Jam Operasional</label>
                            <input type="text" name="operational_hours" x-model="editingFacility.operational_hours" required class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Nomor Telepon</label>
                            <input type="text" name="phone_number" x-model="editingFacility.phone_number" placeholder="Contoh: +62 812 3456 7890" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Deskripsi Fasilitas</label>
                        <textarea name="description" x-model="editingFacility.description" placeholder="Ceritakan tentang fasilitas ini..." class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold min-h-[100px]"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Layanan Tersedia</label>
                        <input type="text" name="available_services" 
                            :value="editingFacility.available_services ? editingFacility.available_services.join(', ') : ''" 
                            placeholder="Pisahkan dengan koma. Contoh: Free Wi-Fi, Kolam Renang" 
                            class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Tags / Label</label>
                        <input type="text" name="tags" 
                            :value="editingFacility.tags ? editingFacility.tags.join(', ') : ''" 
                            placeholder="Pisahkan dengan koma. Contoh: Pemandangan Danau" 
                            class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold">
                    </div>

                    <!-- Panduan Foto Fasilitas -->
                    <div class="bg-emerald-50/50 border border-emerald-100/80 rounded-2xl p-4 text-xs text-gray-600 space-y-2">
                        <div class="flex items-center gap-2 text-[#066466] font-bold">
                            <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Panduan Foto Fasilitas Umum</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <div class="space-y-1">
                                <span class="font-bold text-gray-700 block">1. Foto Utama (Thumbnail / Cover)</span>
                                <p class="leading-relaxed">Akan digunakan sebagai <strong>sampul utama</strong> pada daftar fasilitas umum di aplikasi mobile.</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-bold text-gray-700 block">2. Foto Tambahan (Galeri)</span>
                                <p class="leading-relaxed">Akan ditampilkan sebagai <strong>galeri gambar tambahan</strong> di halaman detail fasilitas umum.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Daftar Foto Saat Ini</label>
                        
                        <!-- Galeri saat ini -->
                        <template x-if="editingFacility?.images_data && editingFacility.images_data.length > 0">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3">
                                <template x-for="(imgObj, idx) in editingFacility.images_data" :key="imgObj.path">
                                    <div class="relative rounded-xl overflow-hidden bg-gray-100 aspect-square group border border-gray-200">
                                        <img :src="imgObj.url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Galeri Fasilitas">
                                        
                                        <!-- Cover / Galeri Badge -->
                                        <div class="absolute top-2 left-2 z-10 px-2 py-0.5 rounded text-[8px] font-bold text-white uppercase tracking-wider shadow-sm"
                                             :class="idx === 0 ? 'bg-[#066466]' : 'bg-gray-800/80'"
                                             x-text="idx === 0 ? 'Cover' : 'Galeri'"></div>

                                        <!-- Tombol Hapus overlay -->
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" @click.stop="
                                                deletedImages.push(imgObj.path); 
                                                editingFacility.images_data = editingFacility.images_data.filter(i => i.path !== imgObj.path);
                                                if (editingFacility.images_data.length > 0) {
                                                    editingFacility.image_url = editingFacility.images_data[0].path;
                                                } else {
                                                    editingFacility.image_url = null;
                                                }
                                            " class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transform hover:scale-110 transition-all shadow-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        
                                        <button type="button" @click.stop="lightboxImage = imgObj.url; showLightbox = true" class="absolute top-2 right-2 bg-black/50 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/70">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!(editingFacility?.images_data && editingFacility.images_data.length > 0) && editingFacility?.image_url">
                            <div class="mb-3">
                                <div class="relative rounded-2xl overflow-hidden bg-gray-100 h-40 w-full border border-gray-100 group cursor-pointer" @click="lightboxImage = (editingFacility.image_url.startsWith('http') ? editingFacility.image_url : '/storage/' + editingFacility.image_url); showLightbox = true" title="Klik untuk memperbesar">
                                    <img :src="editingFacility.image_url.startsWith('http') ? editingFacility.image_url : '/storage/' + editingFacility.image_url" class="w-full h-full object-cover" alt="Foto Saat Ini">
                                    
                                    <!-- Cover Badge -->
                                    <div class="absolute top-2 left-2 z-10 px-2 py-0.5 rounded text-[8px] font-bold text-white uppercase tracking-wider bg-[#066466] shadow-sm">Cover</div>

                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity gap-3">
                                        <button type="button" @click.stop="lightboxImage = (editingFacility.image_url.startsWith('http') ? editingFacility.image_url : '/storage/' + editingFacility.image_url); showLightbox = true" class="bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </button>
                                        <button type="button" @click.stop="
                                            deletedImages.push(editingFacility.image_url); 
                                            editingFacility.image_url = null;
                                        " class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full hover:scale-110 transition-all shadow-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Ganti Foto Utama (Cover) -->
                        <div class="space-y-2" x-data="{ editCoverPreview: '' }">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Ganti Foto Utama (Cover)</label>
                            <div class="relative group">
                                <input type="file" name="image" id="edit_image" class="hidden" 
                                    @change="
                                        editFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                        if ($event.target.files[0]) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => { editCoverPreview = e.target.result; };
                                            reader.readAsDataURL($event.target.files[0]);
                                        } else {
                                            editCoverPreview = '';
                                        }
                                    ">
                                <label for="edit_image" class="relative flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                    <template x-if="editCoverPreview">
                                        <div class="absolute inset-0 w-full h-full bg-gray-100">
                                            <img :src="editCoverPreview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                <p class="text-white text-xs font-bold">Ganti Foto Utama</p>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!editCoverPreview">
                                        <div class="flex flex-col items-center justify-center text-center px-4">
                                            <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                <svg class="w-5 h-5 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-700" x-text="editFileName || 'Pilih foto utama baru'"></p>
                                            <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP (Maks. 5MB)</p>
                                        </div>
                                    </template>
                                </label>
                            </div>
                        </div>

                        <!-- Ganti Foto Tambahan (Galeri) -->
                        <div class="space-y-2" x-data="{ newGalleryPreviews: [] }">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Tambah Foto Galeri</label>
                            <div class="relative group">
                                <input type="file" name="images[]" id="edit_images" multiple class="hidden" 
                                    @change="
                                        newGalleryPreviews = [];
                                        const files = $event.target.files;
                                        for (let i = 0; i < files.length; i++) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => { newGalleryPreviews.push(e.target.result); };
                                            reader.readAsDataURL(files[i]);
                                        }
                                    ">
                                <label for="edit_images" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                    <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700" x-text="newGalleryPreviews.length > 0 ? newGalleryPreviews.length + ' file dipilih' : 'Pilih foto tambahan'"></p>
                                    <p class="text-[10px] text-gray-400 mt-1">Bisa pilih lebih dari 1</p>
                                </label>
                            </div>
                            
                            <!-- Previews -->
                            <template x-if="newGalleryPreviews.length > 0">
                                <div class="grid grid-cols-4 gap-2 mt-2">
                                    <template x-for="(src, idx) in newGalleryPreviews" :key="idx">
                                        <div class="relative rounded-xl overflow-hidden aspect-square border border-gray-200">
                                            <img :src="src" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-50 rounded-2xl flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">Status Aktif</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Aktifkan untuk menampilkan di aplikasi</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            {{-- Hidden input always sends 0; checkbox overrides to 1 when checked --}}
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   :checked="editingFacility.is_active == true || editingFacility.is_active == 1"
                                   @change="editingFacility.is_active = $event.target.checked"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showEditModal = false" class="px-8 py-4 text-sm font-bold text-gray-400 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all">Batal</button>
                        <button type="submit" class="px-10 py-4 bg-sidebar text-white text-sm font-bold rounded-2xl shadow-lg shadow-sidebar/20 hover:opacity-95 transition-all flex items-center gap-2" :disabled="loading">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <!-- Header -->
                <div class="flex items-center justify-between px-10 pt-8 pb-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900">Detail Fasilitas</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Detail Modul</span>
                                            <p class="text-slate-200 font-normal font-sans">Menampilkan informasi lengkap mengenai fasilitas umum beserta galeri foto interaktif dan letak geografisnya pada peta.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 mt-0.5 font-medium">Informasi lengkap mengenai fasilitas umum</p>
                    </div>
                    <button @click="showViewModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-10">
                    <div x-show="loading && !viewingFacility" class="py-12 flex flex-col items-center justify-center gap-4">
                        <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-emerald-600 animate-pulse">Memuat data...</p>
                    </div>

                    <div x-show="viewingFacility" class="space-y-8">
                        <div class="space-y-4">
                            <div class="rounded-[2rem] overflow-hidden bg-gray-100 aspect-video relative group cursor-pointer shadow-sm border border-gray-100" title="Klik untuk memperbesar">
                                <template x-if="viewingFacility?.images_url && viewingFacility.images_url.length > 0">
                                    <div class="w-full h-full" @click="lightboxImage = viewingFacility.images_url[activeViewImageIndex]; showLightbox = true">
                                        <img :src="viewingFacility.images_url[activeViewImageIndex]" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-xl">Klik untuk memperbesar</span>
                                        </div>
                                        <div class="absolute bottom-6 left-6 z-10">
                                            <span class="px-4 py-2 bg-[#066466]/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-white uppercase tracking-widest shadow-sm" x-text="activeViewImageIndex === 0 ? 'Foto Utama (Cover)' : 'Foto Tambahan (Galeri)'"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!(viewingFacility?.images_url && viewingFacility.images_url.length > 0) && viewingFacility?.image_url">
                                    <div class="w-full h-full" @click="lightboxImage = (viewingFacility.image_url.startsWith('http') ? viewingFacility.image_url : '/storage/' + viewingFacility.image_url); showLightbox = true">
                                        <img :src="viewingFacility.image_url.startsWith('http') ? viewingFacility.image_url : '/storage/' + viewingFacility.image_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-xl">Klik untuk memperbesar</span>
                                        </div>
                                        <div class="absolute bottom-6 left-6 z-10">
                                            <span class="px-4 py-2 bg-[#066466]/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-white uppercase tracking-widest shadow-sm">Foto Utama (Cover)</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="viewingFacility !== null && !viewingFacility.image_url && !(viewingFacility.images_url && viewingFacility.images_url.length > 0)">
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs font-bold uppercase tracking-widest">Tidak ada foto</p>
                                    </div>
                                </template>
                                <div class="absolute top-6 right-6 z-10">
                                    <span class="px-4 py-2 bg-white/90 backdrop-blur-md rounded-xl text-[10px] font-bold text-gray-900 uppercase tracking-widest shadow-sm" x-text="viewingFacility?.type || '-'"></span>
                                </div>
                            </div>

                            <!-- Clickable horizontal scroll thumbnail row utilizing activeViewImageIndex -->
                            <template x-if="viewingFacility?.images_url && viewingFacility.images_url.length > 1">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">Galeri Foto (Pilih Gambar)</h4>
                                    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin">
                                        <template x-for="(img, idx) in viewingFacility.images_url" :key="idx">
                                            <button type="button" @click="activeViewImageIndex = idx" 
                                                    class="relative w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 border-2 transition-all"
                                                    :class="activeViewImageIndex === idx ? 'border-[#066466] shadow-md scale-105' : 'border-gray-200 hover:border-gray-300'">
                                                <img :src="img" class="w-full h-full object-cover">
                                                <div class="absolute top-1 left-1 px-1 rounded text-[6px] font-bold text-white uppercase"
                                                     :class="idx === 0 ? 'bg-[#066466]' : 'bg-gray-800/80'"
                                                     x-text="idx === 0 ? 'Cover' : 'Galeri'"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Info Grid -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-900 leading-tight" x-text="viewingFacility?.name || '-'"></h4>
                                <p class="text-sm font-medium text-gray-400 mt-1" x-text="viewingFacility?.address || '-'"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-6 pt-6 border-t border-gray-50">
                                <div>
                                    <h5 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Jam Operasional</h5>
                                    <p class="text-sm font-bold text-emerald-600" x-text="viewingFacility?.operational_hours || '-'"></p>
                                </div>
                                <div>
                                    <h5 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Nomor Telepon</h5>
                                    <p class="text-sm font-bold text-gray-900" x-text="viewingFacility?.phone_number || '-'"></p>
                                </div>
                                <div>
                                    <h5 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Status Aktif</h5>
                                    <template x-if="viewingFacility?.is_active">
                                        <span class="inline-flex items-center gap-1.5 text-emerald-600 text-xs font-bold">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                            Terverifikasi Aktif
                                        </span>
                                    </template>
                                    <template x-if="viewingFacility !== null && !viewingFacility.is_active">
                                        <span class="inline-flex items-center gap-1.5 text-red-500 text-xs font-bold">
                                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                                            Nonaktif
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Map Preview -->
                            <div class="space-y-3 pt-4 border-t border-gray-50">
                                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Kordinat Geografis</h4>
                                <div class="flex items-center gap-4 text-xs font-mono text-gray-500 bg-gray-50 p-4 rounded-2xl">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-400">LAT:</span>
                                        <span x-text="viewingFacility?.latitude || '-'"></span>
                                    </div>
                                    <div class="flex items-center gap-2 border-l border-gray-200 pl-4">
                                        <span class="font-bold text-gray-400">LNG:</span>
                                        <span x-text="viewingFacility?.longitude || '-'"></span>
                                    </div>
                                </div>
                                
                                {{-- Google Map Container for Detail View --}}
                                <div id="view_map_picker" class="w-full mt-4" style="height: 250px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-10 py-6 bg-gray-50 flex items-center justify-between border-t border-gray-100">
                    <div class="flex items-center gap-2">
                         <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                         <p class="text-xs text-gray-400 font-medium">Terakhir diperbarui: <span x-text="viewingFacility?.updated_at ? new Date(viewingFacility.updated_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-'"></span></p>
                    </div>
                    <button @click="showViewModal = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all shadow-sm">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div x-show="showLightbox" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm" x-cloak @click="showLightbox = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative max-w-4xl max-h-[90vh] p-4 flex items-center justify-center" @click.stop>
            <img :src="lightboxImage" class="max-w-[95vw] max-h-[85vh] rounded-3xl object-contain shadow-2xl border border-white/10" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <button @click="showLightbox = false" class="absolute -top-12 right-0 p-3 bg-black/60 text-white rounded-full hover:bg-black/80 transition-colors border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Upload Progress Modal (desain premium) -->
    <div x-show="showUploadProgress" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-50 text-center space-y-6"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <!-- Circular Progress Indicator -->
            <div class="relative flex items-center justify-center mx-auto w-28 h-28">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 112 112">
                    <circle cx="56" cy="56" r="46" stroke="#f3f4f6" stroke-width="8" fill="transparent" />
                    <circle cx="56" cy="56" r="46" stroke="#066466" stroke-width="8" fill="transparent"
                            :stroke-dasharray="2 * Math.PI * 46"
                            :stroke-dashoffset="2 * Math.PI * 46 * (1 - uploadProgressPercent / 100)"
                            stroke-linecap="round" class="transition-all duration-300 ease-out" />
                </svg>
                <span class="absolute text-2xl font-extrabold text-[#066466]" x-text="uploadProgressPercent + '%'"></span>
            </div>
            
            <div class="space-y-2">
                <h4 class="font-extrabold text-gray-800 text-lg">Mengunggah Media...</h4>
                <p class="text-xs text-gray-500 font-semibold leading-relaxed" x-text="uploadProgressText"></p>
                <div x-show="uploadSpeedText" class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 text-teal-700 text-xs font-bold rounded-full">
                    <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span x-text="uploadSpeedText"></span>
                </div>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden relative">
                <div class="bg-gradient-to-r from-teal-600 to-emerald-500 h-full rounded-full transition-all duration-300 ease-out" :style="'width: ' + uploadProgressPercent + '%'"></div>
            </div>
        </div>
    </div>
</div>

<script>
function facilityManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        showViewModal: false,
        viewingFacility: null,
        loading: false,
        createIsActive: true,
        editingFacility: null,
        createFileName: '',
        showLightbox: false,
        lightboxImage: '',
        deletedImages: [],
        activeType: '{{ request('type', 'Semua') }}',
        searchQuery: '{{ request('search', '') }}',
        statusFilter: '{{ request('status', 'all') }}',
        editFileName: '',
        activeViewImageIndex: 0,
        showUploadProgress: false,
        uploadProgressPercent: 0,
        uploadProgressText: '',
        uploadSpeedText: '',

        filterByType(type) {
            this.activeType = type;
            this.fetchData();
        },

        fetchData() {
            const params = new URLSearchParams();
            if (this.activeType !== 'Semua') params.append('type', this.activeType);
            if (this.searchQuery) params.append('search', this.searchQuery);
            if (this.statusFilter !== 'all') params.append('status', this.statusFilter);
            
            window.location.href = `{{ route('admin.fasilitas_umum.index') }}?${params.toString()}`;
        },

        async openViewModal(id) {
            if (!id) return;
            this.showEditModal = false;
            this.loading = true;
            this.viewingFacility = null;
            this.activeViewImageIndex = 0;
            try {
                const response = await fetch(`/admin/fasilitas-umum/${id}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await window.safeParseJSON(response);
                if (data) {
                    this.viewingFacility = data;
                    this.showViewModal = true;
                } else {
                    throw new Error('Data tidak valid');
                }
            } catch (error) {
                console.error(error);
                if (error.message && error.message !== 'Unexpected token < in JSON at position 0') {
                    window.showAlert(error.message, 'Error', 'error');
                } else {
                    window.showAlert('Gagal mengambil detail fasilitas', 'Error', 'error');
                }
                this.showViewModal = false;
            } finally {
                this.loading = false;
            }
        },

        async openEditModal(id) {
            if (!id) return;
            this.showViewModal = false;
            this.loading = true;
            this.editingFacility = null;
            this.editFileName = '';
            this.deletedImages = [];
            try {
                const response = await fetch(`/admin/fasilitas-umum/${id}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await window.safeParseJSON(response);
                if (data && !data._id && data.id) data._id = data.id;
                this.editingFacility = data;
                this.showEditModal = true;
            } catch (error) {
                console.error(error);
                if (error.message && error.message !== 'Unexpected token < in JSON at position 0') {
                    window.showAlert(error.message, 'Error', 'error');
                } else {
                    window.showAlert('Gagal mengambil data fasilitas', 'Error', 'error');
                }
                this.showEditModal = false;
            } finally {
                this.loading = false;
            }
        },

        uploadToCloudinaryDirectly(file, signData) {
            return new Promise((resolve, reject) => {
                let resourceType = 'image';
                if (file.type) {
                    if (file.type.startsWith('video/')) {
                        resourceType = 'video';
                    }
                } else if (file.name) {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (['mp4', 'mov', 'avi', 'webm', 'ogg', 'mkv', '3gp', 'wmv', 'flv'].includes(ext)) {
                        resourceType = 'video';
                    }
                }
                const uploadUrl = `https://api.cloudinary.com/v1_1/${signData.cloud_name}/${resourceType}/upload`;
                
                const chunkSize = 10 * 1024 * 1024; // 10MB chunk size
                const totalSize = file.size;
                
                if (totalSize <= chunkSize) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', uploadUrl);
                    
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('api_key', signData.api_key);
                    formData.append('timestamp', signData.timestamp);
                    formData.append('signature', signData.signature);
                    formData.append('folder', signData.folder);
                    
                    let startTime = Date.now();
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            this.uploadProgressPercent = percent;
                            const loadedMB = (e.loaded / (1024 * 1024)).toFixed(1);
                            const totalMB = (e.total / (1024 * 1024)).toFixed(1);
                            this.uploadProgressText = `Mengunggah media ke Cloudinary: ${loadedMB} MB dari ${totalMB} MB`;
                            
                            const elapsed = (Date.now() - startTime) / 1000;
                            if (elapsed > 0) {
                                const speed = e.loaded / elapsed;
                                this.uploadSpeedText = speed > 1024 * 1024 
                                    ? `Kecepatan: ${(speed / (1024 * 1024)).toFixed(1)} MB/detik`
                                    : `Kecepatan: ${(speed / 1024).toFixed(0)} KB/detik`;
                            }
                        }
                    });
                    
                    xhr.onload = () => {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            resolve(JSON.parse(xhr.responseText));
                        } else {
                            let errorMsg = 'Gagal mengunggah ke Cloudinary';
                            try {
                                const errObj = JSON.parse(xhr.responseText);
                                if (errObj.error && errObj.error.message) {
                                    errorMsg = errObj.error.message;
                                }
                            } catch(e) {}
                            reject(new Error(errorMsg));
                        }
                    };
                    xhr.onerror = () => reject(new Error('Koneksi internet bermasalah.'));
                    xhr.send(formData);
                } else {
                    const uploadId = 'upload_' + Math.random().toString(36).substring(2, 15);
                    let start = 0;
                    let startTime = Date.now();
                    
                    const uploadNextChunk = async () => {
                        if (start >= totalSize) return;
                        
                        const end = Math.min(start + chunkSize, totalSize);
                        const chunk = file.slice(start, end);
                        
                        const formData = new FormData();
                        formData.append('file', chunk, file.name);
                        formData.append('api_key', signData.api_key);
                        formData.append('timestamp', signData.timestamp);
                        formData.append('signature', signData.signature);
                        formData.append('folder', signData.folder);
                        
                        return new Promise((resChunk, rejChunk) => {
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', uploadUrl);
                            
                            xhr.setRequestHeader('X-Unique-Upload-Id', uploadId);
                            xhr.setRequestHeader('Content-Range', `bytes ${start}-${end-1}/${totalSize}`);
                            
                            xhr.upload.addEventListener('progress', (e) => {
                                if (e.lengthComputable) {
                                    const chunkProgress = e.loaded / e.total;
                                    const currentLoaded = start + chunkProgress * (end - start);
                                    const percent = Math.round((currentLoaded / totalSize) * 100);
                                    this.uploadProgressPercent = percent;
                                    
                                    const loadedMB = (currentLoaded / (1024 * 1024)).toFixed(1);
                                    const totalMB = (totalSize / (1024 * 1024)).toFixed(1);
                                    this.uploadProgressText = `Mengunggah video ke Cloudinary (Bagian ${(Math.floor(start / chunkSize) + 1)}): ${loadedMB} MB dari ${totalMB} MB`;
                                    
                                    const elapsed = (Date.now() - startTime) / 1000;
                                    if (elapsed > 0) {
                                        const speed = currentLoaded / elapsed;
                                        this.uploadSpeedText = speed > 1024 * 1024 
                                            ? `Kecepatan: ${(speed / (1024 * 1024)).toFixed(1)} MB/detik`
                                            : `Kecepatan: ${(speed / 1024).toFixed(0)} KB/detik`;
                                    }
                                }
                            });
                            
                            xhr.onload = () => {
                                if (xhr.status >= 200 && xhr.status < 300) {
                                    const result = JSON.parse(xhr.responseText);
                                    start = end;
                                    resChunk(result);
                                } else {
                                    let errorMsg = 'Gagal mengunggah bagian video';
                                    try {
                                        const errObj = JSON.parse(xhr.responseText);
                                        if (errObj.error && errObj.error.message) {
                                            errorMsg = errObj.error.message;
                                        }
                                    } catch(e) {}
                                    rejChunk(new Error(errorMsg));
                                }
                            };
                            xhr.onerror = () => rejChunk(new Error('Koneksi terputus saat mengunggah video.'));
                            xhr.send(formData);
                        });
                    };
                    
                    let finalResult = null;
                    (async () => {
                        try {
                            while (start < totalSize) {
                                finalResult = await uploadNextChunk();
                            }
                            resolve(finalResult);
                        } catch (err) {
                            reject(err);
                        }
                    })();
                }
            });
        },

        uploadToLocalWithProgress(formData, url) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').getAttribute('content'));
                
                let startTime = Date.now();
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        this.uploadProgressPercent = percent;
                        
                        const loadedMB = (e.loaded / (1024 * 1024)).toFixed(1);
                        const totalMB = (e.total / (1024 * 1024)).toFixed(1);
                        this.uploadProgressText = `Mengunggah media ke server lokal: ${loadedMB} MB dari ${totalMB} MB`;
                        
                        const elapsed = (Date.now() - startTime) / 1000;
                        if (elapsed > 0) {
                            const speed = e.loaded / elapsed;
                            this.uploadSpeedText = speed > 1024 * 1024 
                                ? `Kecepatan: ${(speed / (1024 * 1024)).toFixed(1)} MB/detik`
                                : `Kecepatan: ${(speed / 1024).toFixed(0)} KB/detik`;
                        }
                    }
                });
                
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        try {
                            const errRes = JSON.parse(xhr.responseText);
                            reject(new Error(errRes.message || 'Gagal menyimpan data ke server'));
                        } catch(e) {
                            reject(new Error('Gagal menyimpan data ke server (Status: ' + xhr.status + ')'));
                        }
                    }
                };
                xhr.onerror = () => reject(new Error('Koneksi terputus ke server lokal.'));
                xhr.send(formData);
            });
        },

        async submitCreate() {
            this.loading = true;
            const form = document.getElementById('createFacilityForm');
            const thumbnailInput = document.getElementById('create_image');
            const imagesInput = document.getElementById('create_images');
            
            try {
                const signRes = await fetch('/admin/carousel-banners/sign-upload?module=fasilitas_umum');
                if (!signRes.ok) {
                    throw new Error('Gagal mendapatkan izin unggah dari server.');
                }
                const signData = await signRes.json();
                
                const formData = new FormData(form);
                
                if (signData.success && signData.mode === 'cloudinary') {
                    this.showUploadProgress = true;
                    
                    // Upload thumbnail/cover
                    if (thumbnailInput && thumbnailInput.files.length > 0) {
                        this.uploadProgressPercent = 0;
                        this.uploadProgressText = 'Menghubungkan ke Cloudinary untuk mengunggah cover...';
                        this.uploadSpeedText = '';
                        const res = await this.uploadToCloudinaryDirectly(thumbnailInput.files[0], signData);
                        formData.set('image', res.secure_url);
                    }
                    
                    // Upload additional images
                    if (imagesInput && imagesInput.files.length > 0) {
                        formData.delete('images[]');
                        for (let i = 0; i < imagesInput.files.length; i++) {
                            const file = imagesInput.files[i];
                            this.uploadProgressPercent = 0;
                            this.uploadProgressText = `Mengunggah media galeri ${i + 1} dari ${imagesInput.files.length}...`;
                            this.uploadSpeedText = '';
                            const res = await this.uploadToCloudinaryDirectly(file, signData);
                            formData.append('images[]', res.secure_url);
                        }
                    }
                    
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Unggah media berhasil! Menyimpan data ke server...';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                    
                    const response = await fetch('{{ route('admin.fasilitas_umum.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const result = await window.safeParseJSON(response);
                    if (response.ok && result && result.success) {
                        localStorage.setItem('pending_success_toast', result.message || 'Fasilitas umum berhasil ditambahkan');
                        window.location.reload();
                    } else {
                        window.showAlert(result?.message || 'Gagal menambahkan fasilitas', 'Gagal', 'error');
                    }
                } else {
                    // Fallback to local upload with progress
                    this.showUploadProgress = true;
                    this.uploadProgressPercent = 0;
                    this.uploadProgressText = 'Menghubungkan ke server lokal...';
                    this.uploadSpeedText = '';
                    
                    const result = await this.uploadToLocalWithProgress(formData, '{{ route('admin.fasilitas_umum.store') }}');
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Berhasil disimpan!';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                    
                    if (result.success) {
                        localStorage.setItem('pending_success_toast', result.message || 'Fasilitas umum berhasil ditambahkan');
                        window.location.reload();
                    } else {
                        window.showAlert(result.message || 'Gagal menyimpan fasilitas', 'Gagal', 'error');
                    }
                }
            } catch (error) {
                console.error(error);
                this.showUploadProgress = false;
                window.showAlert(error.message || 'Terjadi kesalahan saat menyimpan data', 'Error', 'error');
            } finally {
                this.loading = false;
            }
        },

        async submitUpdate() {
            const facilityId = this.editingFacility?._id || this.editingFacility?.id;
            if (!facilityId) {
                window.showAlert('ID Fasilitas tidak ditemukan', 'Perhatian', 'warning');
                return;
            }

            this.loading = true;
            const form = document.getElementById('editFacilityForm');
            const thumbnailInput = document.getElementById('edit_image');
            const imagesInput = document.getElementById('edit_images');
            
            try {
                const signRes = await fetch('/admin/carousel-banners/sign-upload?module=fasilitas_umum');
                if (!signRes.ok) {
                    throw new Error('Gagal mendapatkan izin unggah dari server.');
                }
                const signData = await signRes.json();
                
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                this.deletedImages.forEach(img => {
                    formData.append('delete_images[]', img);
                });

                // Explicitly set is_active from Alpine state (overrides any checkbox ambiguity)
                formData.delete('is_active');
                formData.append('is_active', this.editingFacility.is_active ? '1' : '0');
                
                if (signData.success && signData.mode === 'cloudinary') {
                    this.showUploadProgress = true;
                    
                    // Upload cover
                    if (thumbnailInput && thumbnailInput.files.length > 0) {
                        this.uploadProgressPercent = 0;
                        this.uploadProgressText = 'Menghubungkan ke Cloudinary untuk mengunggah cover...';
                        this.uploadSpeedText = '';
                        const res = await this.uploadToCloudinaryDirectly(thumbnailInput.files[0], signData);
                        formData.set('image', res.secure_url);
                    }
                    
                    // Upload additional images
                    if (imagesInput && imagesInput.files.length > 0) {
                        formData.delete('images[]');
                        for (let i = 0; i < imagesInput.files.length; i++) {
                            const file = imagesInput.files[i];
                            this.uploadProgressPercent = 0;
                            this.uploadProgressText = `Mengunggah media galeri ${i + 1} dari ${imagesInput.files.length}...`;
                            this.uploadSpeedText = '';
                            const res = await this.uploadToCloudinaryDirectly(file, signData);
                            formData.append('images[]', res.secure_url);
                        }
                    }
                    
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Unggah media berhasil! Menyimpan data ke server...';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                    
                    const response = await fetch(`/admin/fasilitas-umum/${facilityId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const result = await window.safeParseJSON(response);
                    if (response.ok && result && result.success) {
                        localStorage.setItem('pending_success_toast', result.message || 'Fasilitas umum berhasil diperbarui');
                        window.location.reload();
                    } else {
                        window.showAlert(result?.message || 'Gagal memperbarui fasilitas', 'Gagal', 'error');
                    }
                } else {
                    // Fallback to local upload with progress
                    this.showUploadProgress = true;
                    this.uploadProgressPercent = 0;
                    this.uploadProgressText = 'Menghubungkan ke server lokal...';
                    this.uploadSpeedText = '';
                    
                    const result = await this.uploadToLocalWithProgress(formData, `/admin/fasilitas-umum/${facilityId}`);
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Berhasil disimpan!';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                    
                    if (result.success) {
                        localStorage.setItem('pending_success_toast', result.message || 'Fasilitas umum berhasil diperbarui');
                        window.location.reload();
                    } else {
                        window.showAlert(result.message || 'Gagal menyimpan fasilitas', 'Gagal', 'error');
                    }
                }
            } catch (error) {
                console.error(error);
                this.showUploadProgress = false;
                window.showAlert(error.message || 'Terjadi kesalahan saat menyimpan data', 'Error', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}

// Map Initialization and Logic
let createMap, editMap, viewMap, createMarker, editMarker, viewMarker;

function initGoogleMap(elementId, latId, lngId, initialPos = { lat: 2.3361, lng: 99.0631 }) {
    if (typeof google === 'undefined') return null;

    const mapElement = document.getElementById(elementId);
    if (!mapElement) return null;

    const map = new google.maps.Map(mapElement, {
        zoom: 14,
        center: initialPos,
        mapTypeControl: false,
        streetViewControl: false,
    });

    const marker = new google.maps.Marker({
        position: initialPos,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
    });

    const updateInputs = (pos) => {
        const latInput = document.getElementById(latId);
        const lngInput = document.getElementById(lngId);
        if (latInput) {
            latInput.value = pos.lat().toFixed(8);
            latInput.dispatchEvent(new Event('input'));
        }
        if (lngInput) {
            lngInput.value = pos.lng().toFixed(8);
            lngInput.dispatchEvent(new Event('input'));
        }
    };

    // --- Location Search Feature using standard Autocomplete ---
    const isCreate = elementId.includes('create');
    const searchInput = document.getElementById(isCreate ? 'create_location_search' : 'edit_location_search');

    if (searchInput && typeof google.maps.places.Autocomplete !== 'undefined') {
        const autocomplete = new google.maps.places.Autocomplete(searchInput, {
            componentRestrictions: { country: 'id' },
            fields: ['geometry', 'formatted_address', 'name'],
            types: ['geocode', 'establishment']
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) return;

            const pos = place.geometry.location;
            map.setCenter(pos);
            map.setZoom(17);
            marker.setPosition(pos);
            updateInputs(pos);
            searchInput.value = place.formatted_address || place.name;
        });

        // Handle Enter key
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(isCreate ? 'create_location_search' : 'edit_location_search', elementId);
            }
        });
    }
    // --- End Search Feature ---

    setTimeout(() => {
        google.maps.event.trigger(map, "resize");
        map.setCenter(initialPos);
    }, 300);

    map.addListener("click", (e) => {
        marker.setPosition(e.latLng);
        updateInputs(e.latLng);
    });

    marker.addListener("dragend", () => {
        updateInputs(marker.getPosition());
    });

    return { map, marker };
}

function initGoogleMapReadOnly(elementId, initialPos = { lat: 2.3361, lng: 99.0631 }) {
    if (typeof google === 'undefined') {
        console.warn('Google Maps API not yet loaded');
        return null;
    }

    const mapElement = document.getElementById(elementId);
    if (!mapElement) return null;

    const map = new google.maps.Map(mapElement, {
        zoom: 15,
        center: initialPos,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        zoomControl: true,
    });

    const marker = new google.maps.Marker({
        position: initialPos,
        map: map,
        draggable: false,
        animation: google.maps.Animation.DROP,
    });

    // Ensure map renders correctly
    setTimeout(() => {
        google.maps.event.trigger(map, "resize");
        map.setCenter(initialPos);
    }, 300);

    return { map, marker };
}

// Monitor Alpine.js for modal changes
setInterval(() => {
    const el = document.getElementById('facility-manager');
    if (el && window.Alpine) {
        const data = Alpine.$data(el);
        if (data) {
            // Create Map
            if (data.showCreateModal && !createMap && typeof google !== 'undefined') {
                createMap = true;
                setTimeout(() => {
                    const res = initGoogleMap('create_map_picker', 'create_latitude', 'create_longitude');
                    if(res) { createMap = res.map; createMarker = res.marker; }
                    else { createMap = null; }
                }, 500);
            } else if (!data.showCreateModal) { createMap = null; }

            // Edit Map
            if (data.showEditModal && data.editingFacility && !editMap && typeof google !== 'undefined') {
                editMap = true;
                setTimeout(() => {
                    const pos = { 
                        lat: parseFloat(data.editingFacility.latitude) || 2.3361, 
                        lng: parseFloat(data.editingFacility.longitude) || 99.0631 
                    };
                    const res = initGoogleMap('edit_map_picker', 'edit_latitude', 'edit_longitude', pos);
                    if(res) { editMap = res.map; editMarker = res.marker; }
                    else { editMap = null; }
                }, 500);
            } else if (!data.showEditModal) { editMap = null; }

            // View Map (Read-Only)
            if (data.showViewModal && data.viewingFacility && !viewMap && typeof google !== 'undefined') {
                viewMap = true;
                setTimeout(() => {
                    const pos = { 
                        lat: parseFloat(data.viewingFacility.latitude) || 2.3361, 
                        lng: parseFloat(data.viewingFacility.longitude) || 99.0631 
                    };
                    const res = initGoogleMapReadOnly('view_map_picker', pos);
                    if(res) { viewMap = res.map; viewMarker = res.marker; }
                    else { viewMap = null; }
                }, 500);
            } else if (!data.showViewModal) { viewMap = null; }
        }
    }
}, 500);

// Get current user location
function getCurrentLocation(latId, lngId, mapElementId) {
    if (!navigator.geolocation) {
        alert("Geolocation tidak didukung oleh browser Anda.");
        return;
    }

    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            const latInput = document.getElementById(latId);
            const lngInput = document.getElementById(lngId);
            if (latInput) latInput.value = pos.lat.toFixed(8);
            if (lngInput) lngInput.value = pos.lng.toFixed(8);

            // Update Map & Marker
            const isCreate = mapElementId.includes('create');
            const map = isCreate ? createMap : editMap;
            const marker = isCreate ? createMarker : editMarker;

            if (map && marker && typeof map !== 'boolean') {
                map.setCenter(pos);
                map.setZoom(17);
                marker.setPosition(pos);
            }

            btn.innerHTML = originalContent;
            btn.disabled = false;
        },
        (error) => {
            console.error("Geolocation error:", error);
            alert("Gagal mengambil lokasi: " + error.message);
            btn.innerHTML = originalContent;
            btn.disabled = false;
        },
        { enableHighAccuracy: true }
    );
}

// Perform Geocoding Search
function performSearch(inputId, mapElementId) {
    const query = document.getElementById(inputId).value;
    if (!query || query.trim().length < 3) return;

    const isCreate = mapElementId.includes('create');
    const map = isCreate ? createMap : editMap;
    const marker = isCreate ? createMarker : editMarker;
    const latId = isCreate ? 'create_latitude' : 'edit_latitude';
    const lngId = isCreate ? 'create_longitude' : 'edit_longitude';

    if (!map || typeof map === 'boolean') return;

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 
        address: query, 
        componentRestrictions: { country: 'id' } 
    }, (results, status) => {
        if (status === 'OK' && results[0]) {
            const pos = results[0].geometry.location;
            map.setCenter(pos);
            map.setZoom(17);
            marker.setPosition(pos);
            
            const latInput = document.getElementById(latId);
            const lngInput = document.getElementById(lngId);
            if (latInput) latInput.value = pos.lat().toFixed(8);
            if (lngInput) lngInput.value = pos.lng().toFixed(8);
            
            document.getElementById(inputId).value = results[0].formatted_address;
        } else {
            console.warn('Geocode failed:', status);
        }
    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=Function.prototype" async defer></script>
@endsection
