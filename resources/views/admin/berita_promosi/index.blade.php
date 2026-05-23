@extends('admin.layouts.app')

@section('title', 'Berita & Promosi')
@section('page_title', 'Berita & Promosi')
@section('page_description', 'Kelola publikasi informasi, artikel, dan promo menarik di Danau Toba.')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Berita & Promosi</span>
</nav>
@endsection

@section('page_actions')
<button type="button" onclick="document.querySelector('[data-open-create-modal]')?.click()" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
    Tambah Berita/Promosi
</button>
@endsection

@section('content')
<div x-data="{
    showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('_method') == 'PUT' ? 'true' : 'false' }},
    showViewModal: false,
    loading: false,
    viewingItem: null,
    editPreviewUrl: '',
    createPreviewUrl: '',
    createFileName: '',
    editFileName: '',
    showLightbox: false,
    lightboxImage: '',
    editingItem: null,
    deletedImages: [],
    activeViewImageIndex: 0,

    async openViewModal(id) {
        if (!id) return;
        this.loading = true;
        this.showViewModal = true;
        this.viewingItem = null;
        this.activeViewImageIndex = 0;
        try {
            const response = await fetch(`/admin/berita-promosi/${id}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await window.safeParseJSON(response);
            if (data) {
                this.viewingItem = data;
            } else {
                throw new Error('Data tidak valid');
            }
        } catch (error) {
            console.error('View error:', error);
            alert('Gagal memuat detail konten');
            this.showViewModal = false;
        } finally {
            this.loading = false;
        }
    },

    async openEditModal(id) {
        if (!id) return;
        this.loading = true;
        this.showEditModal = true;
        try {
            const response = await fetch(`/admin/berita-promosi/${id}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await window.safeParseJSON(response);
            this.editingItem = data;
            this.deletedImages = [];
            
            // Populate form
            const form = document.getElementById('editForm');
            form.action = `/admin/berita-promosi/${id}`;
            document.getElementById('edit_judul').value = data.judul || '';
            document.getElementById('edit_tipe').value = data.tipe || 'BERITA';
            document.getElementById('edit_konten').value = data.konten || '';
            document.getElementById('edit_tanggal').value = data.tanggal_tayang_formatted || '';
            document.getElementById('edit_status').checked = data.is_active;
            this.editPreviewUrl = data.thumbnail_url || '';
            this.editFileName = data.thumbnail ? 'Thumbnail saat ini' : '';
        } catch (error) {
            console.error('Edit error:', error);
            alert('Gagal mengambil data untuk edit');
            this.showEditModal = false;
        } finally {
            this.loading = false;
        }
    }
}">
    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true; createPreviewUrl = ''; createFileName = '';"></button>
    <div class="mb-6"></div>

    <!-- Filters -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
        <form action="{{ route('admin.berita_promosi.index') }}" method="GET" class="space-y-4" id="filter-form">
            <!-- Persist current sorting -->
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'tanggal_tayang') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kata Kunci -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Kata Kunci
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Menyaring daftar berita & promosi berdasarkan kata kunci pada judul.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                        <p class="text-slate-200 font-normal">Pencarian berita dan promosi di Panel Admin.</p>
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
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita/promo..."
                            class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm transition-all shadow-sm placeholder-gray-300">
                    </div>
                </div>

                <!-- Tipe -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Tipe
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Memilah tipe konten antara Berita (informasi umum/artikel) atau Promo (diskon/penawaran spesial).</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Halaman Berita & Promosi pada aplikasi mobile wisatawan.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <select name="tipe" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-sidebar transition-all cursor-pointer">
                        <option value="">Semua Tipe</option>
                        <option value="BERITA" {{ request('tipe') == 'BERITA' ? 'selected' : '' }}>Berita</option>
                        <option value="PROMO" {{ request('tipe') == 'PROMO' ? 'selected' : '' }}>Promo</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Status
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Menyaring konten berdasarkan status publikasi (Aktif/Terbit atau Draft/Nonaktif).</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Tampilan daftar berita dan promo aplikasi mobile (hanya status Aktif yang tampil).</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-sidebar transition-all cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <!-- Tampilkan -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        Tampilkan
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Menentukan jumlah baris data berita dan promosi yang ditampilkan per halaman.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                        <p class="text-slate-200 font-normal">Pagination tabel berita & promosi di Panel Admin.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </label>
                    <div class="flex items-center gap-2">
                        <select name="per_page" onchange="this.form.submit()" 
                            class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm font-bold text-gray-700 shadow-sm hover:border-sidebar transition-all cursor-pointer">
                            @foreach([15, 30, 50, 100] as $val)
                                <option value="{{ $val }}" @selected(request('per_page', 15) == $val)>{{ $val }}</option>
                            @endforeach
                        </select>
                        @if(request('search') || request('tipe') || request('status') || request('per_page') != 15)
                            <a href="{{ route('admin.berita_promosi.index') }}" class="px-4 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
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
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    @php
                        $currentSort = request('sort_by', 'tanggal_tayang');
                        $sortOrder = request('sort_order', 'desc') === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'judul', 'sort_order' => ($currentSort === 'judul' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                JUDUL
                                <svg class="w-4 h-4 {{ $currentSort === 'judul' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'judul' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tipe', 'sort_order' => ($currentSort === 'tipe' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                TIPE
                                <svg class="w-4 h-4 {{ $currentSort === 'tipe' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'tipe' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_tayang', 'sort_order' => ($currentSort === 'tanggal_tayang' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                TANGGAL TAYANG
                                <svg class="w-4 h-4 {{ $currentSort === 'tanggal_tayang' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'tanggal_tayang' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">STATUS</th>
                        <th class="px-10 py-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($beritaPromosi as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-5 text-sm text-gray-500 font-medium">
                            {{ ($beritaPromosi->currentPage() - 1) * $beritaPromosi->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                @if($item->thumbnail)
                                    <img src="{{ image_url($item->thumbnail) }}" @click.stop="lightboxImage = '{{ image_url($item->thumbnail) }}'; showLightbox = true" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-gray-100 cursor-pointer group-hover:scale-105 transition-transform" alt="" title="Klik untuk memperbesar">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-900 max-w-[250px] truncate" title="{{ $item->judul }}">{{ $item->judul }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5 uppercase tracking-wider font-semibold">{{ \Illuminate\Support\Str::limit(strip_tags($item->konten), 40) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1 text-[10px] font-bold rounded-lg tracking-wider {{ $item->tipe === 'PROMO' ? 'bg-orange-50 text-orange-600' : 'bg-teal-50 text-teal-600' }}">
                                {{ $item->tipe }}
                            </span>
                        </td>
                        <td class="px-10 py-6">
                            <p class="text-sm text-gray-600 font-medium">{{ \Carbon\Carbon::parse($item->tanggal_tayang)->translatedFormat('d F Y') }}</p>
                        </td>
                        <td class="px-10 py-6">
                            @if($item->is_active)
                                <span class="px-4 py-1.5 rounded-xl text-xs font-bold bg-[#E6F6F2] text-[#00A884]">Aktif</span>
                            @else
                                <span class="px-4 py-1.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-400">Draft</span>
                            @endif
                        </td>
                        <td class="px-10 py-6 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" @click="openViewModal('{{ (string)$item->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button type="button" @click="openEditModal('{{ (string)$item->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button type="button" 
                                    @click="$dispatch('open-delete-modal', { 
                                        action: '{{ route('admin.berita_promosi.destroy', (string)$item->_id) }}', 
                                        title: 'Hapus Berita/Promosi', 
                                        type: 'berita', 
                                        name: '{{ addslashes($item->judul) }}' 
                                    })" 
                                    class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada entri data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($beritaPromosi->hasPages())
        <div class="px-10 py-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
            <div class="text-sm text-gray-500 font-medium">
                Menampilkan {{ $beritaPromosi->firstItem() }} - {{ $beritaPromosi->lastItem() }} dari {{ $beritaPromosi->total() }} data
            </div>
            <div>
                {{ $beritaPromosi->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @endif
    </div>

    {{-- CREATE MODAL --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <template x-if="showCreateModal">
              <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Tambah Berita/Promosi</h3>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Tambah Berita/Promosi</span>
                                        <p class="text-slate-200 font-normal">Formulir untuk mempublikasikan artikel berita atau promo penawaran spesial. Perubahan akan disinkronkan langsung ke aplikasi wisatawan.</p>
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
                
                <form action="{{ route('admin.berita_promosi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Judul</label>
                            <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Masukkan judul berita atau promosi" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('judul') border-red-500 @enderror">
                            @error('judul') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tipe</label>
                            <select name="tipe" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm appearance-none bg-white @error('tipe') border-red-500 @enderror">
                                <option value="BERITA" {{ old('tipe') == 'BERITA' ? 'selected' : '' }}>BERITA</option>
                                <option value="PROMO" {{ old('tipe') == 'PROMO' ? 'selected' : '' }}>PROMO</option>
                            </select>
                            @error('tipe') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Panduan Manajemen Foto -->
                        <div class="bg-emerald-50/50 border border-emerald-100/80 rounded-2xl p-4 text-xs text-gray-600 space-y-2">
                            <div class="flex items-center gap-2 text-[#066466] font-bold">
                                <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Panduan Foto Berita & Promosi</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block">1. Foto Utama (Thumbnail / Cover)</span>
                                    <p class="leading-relaxed">Akan digunakan sebagai <strong>sampul utama</strong> pada daftar berita/promo di aplikasi mobile.</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block">2. Foto Tambahan (Galeri)</span>
                                    <p class="leading-relaxed">Akan ditampilkan sebagai <strong>galeri gambar tambahan</strong> di halaman detail berita/promo.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2" x-data="{ thumbPreview: '' }">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Foto Utama (Thumbnail)</label>
                                <div class="relative group">
                                    <input type="file" name="thumbnail" id="create_thumbnail" required class="hidden" 
                                           @change="
                                               createFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                               if ($event.target.files[0]) {
                                                   const reader = new FileReader();
                                                   reader.onload = (e) => { thumbPreview = e.target.result; };
                                                   reader.readAsDataURL($event.target.files[0]);
                                               } else {
                                                   thumbPreview = '';
                                               }
                                           ">
                                    <label for="create_thumbnail" class="relative flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                        <template x-if="thumbPreview">
                                            <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                <img :src="thumbPreview" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <p class="text-white text-xs font-bold">Ganti Foto Utama</p>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!thumbPreview">
                                            <div class="flex flex-col items-center justify-center text-center px-4">
                                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                    <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                </div>
                                                <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Pilih foto utama'"></p>
                                                <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP (Maks. 2MB)</p>
                                            </div>
                                        </template>
                                    </label>
                                </div>
                            </div>
                            <div class="space-y-2" x-data="{ galleryPreviews: [] }">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Foto Tambahan (Gallery)</label>
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
                                    <label for="create_images" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                        <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700" x-text="galleryPreviews.length > 0 ? galleryPreviews.length + ' file dipilih' : 'Pilih foto tambahan'"></p>
                                        <p class="text-[10px] text-gray-400 mt-1">Bisa pilih lebih dari 1</p>
                                    </label>
                                </div>
                                
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
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Konten</label>
                            <textarea name="konten" required rows="4" placeholder="Tulis konten berita/promosi di sini..." class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('konten') border-red-500 @enderror">{{ old('konten') }}</textarea>
                            @error('konten') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Tayang</label>
                            <input type="date" name="tanggal_tayang" value="{{ old('tanggal_tayang') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('tanggal_tayang') border-red-500 @enderror">
                            @error('tanggal_tayang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <span class="text-sm font-medium text-gray-700">Status Aktif <span class="text-xs text-gray-500 font-normal ml-1">(Draft jika nonaktif)</span></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateModal = false" class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebar-hover text-white rounded-xl transition-colors text-sm font-medium shadow-lg shadow-sidebar/20">Simpan Konten</button>
                    </div>
                </form>
              </div>
            </template>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false; editingItem = null"></div>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Edit Berita/Promosi</h3>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Edit Berita/Promosi</span>
                                        <p class="text-slate-200 font-normal">Formulir untuk memperbarui artikel berita atau promo penawaran spesial. Perubahan akan langsung disinkronkan ke aplikasi wisatawan.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button @click="showEditModal = false; editingItem = null" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div x-show="loading" class="py-12 flex flex-col items-center justify-center gap-4">
                    <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                    <p class="text-sm font-bold text-emerald-600 animate-pulse">Memuat data...</p>
                </div>

                <form id="editForm" x-show="!loading" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Judul</label>
                            <input type="text" id="edit_judul" name="judul" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('judul') border-red-500 @enderror">
                            @error('judul') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tipe</label>
                            <select id="edit_tipe" name="tipe" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm appearance-none bg-white @error('tipe') border-red-500 @enderror">
                                <option value="BERITA">BERITA</option>
                                <option value="PROMO">PROMO</option>
                            </select>
                            @error('tipe') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Panduan Manajemen Foto -->
                        <div class="bg-emerald-50/50 border border-emerald-100/80 rounded-2xl p-4 text-xs text-gray-600 space-y-2">
                            <div class="flex items-center gap-2 text-[#066466] font-bold">
                                <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Panduan Foto Berita & Promosi</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block">1. Foto Utama (Thumbnail / Cover)</span>
                                    <p class="leading-relaxed">Akan digunakan sebagai <strong>sampul utama</strong> pada daftar berita/promo di aplikasi mobile.</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block">2. Foto Tambahan (Galeri)</span>
                                    <p class="leading-relaxed">Akan ditampilkan sebagai <strong>galeri gambar tambahan</strong> di halaman detail berita/promo.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Daftar Foto Saat Ini</label>
                            <!-- Galeri Foto Saat Ini -->
                            <template x-if="editingItem?.images_data && editingItem.images_data.length > 0">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3">
                                    <template x-for="(imgObj, index) in editingItem.images_data" :key="imgObj.path">
                                        <div class="relative rounded-xl overflow-hidden bg-gray-100 aspect-square group border border-gray-200">
                                            <img :src="imgObj.url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Galeri">
                                            
                                            <!-- Badge overlay Cover vs Galeri -->
                                            <div class="absolute top-2 left-2 px-2 py-0.5 rounded text-[8px] font-bold text-white uppercase"
                                                 :class="index === 0 ? 'bg-[#066466]' : 'bg-gray-800/80'"
                                                 x-text="index === 0 ? 'Cover' : 'Galeri'"></div>

                                            <!-- Tombol Hapus overlay -->
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button type="button" @click.stop="
                                                    deletedImages.push(imgObj.path); 
                                                    editingItem.images_data = editingItem.images_data.filter(i => i.path !== imgObj.path);
                                                    if (editingItem.images_data.length > 0) {
                                                        editingItem.thumbnail = editingItem.images_data[0].path;
                                                        editingItem.thumbnail_url = editingItem.images_data[0].url;
                                                    } else {
                                                        editingItem.thumbnail = null;
                                                        editingItem.thumbnail_url = null;
                                                    }
                                                " class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transform hover:scale-110 transition-all shadow-lg">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                            
                                            <!-- Preview button (Top Right) -->
                                            <button type="button" @click.stop="lightboxImage = imgObj.url; showLightbox = true" class="absolute top-2 right-2 bg-black/50 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/70">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!(editingItem?.images_data && editingItem.images_data.length > 0) && editingItem?.thumbnail_url">
                                <div class="mb-3 relative rounded-2xl overflow-hidden bg-gray-100 h-40 w-full border border-gray-100 group">
                                    <img :src="editingItem.thumbnail_url" class="w-full h-full object-cover" alt="Foto Preview">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                        <button type="button" @click.stop="lightboxImage = editingItem.thumbnail_url; showLightbox = true" class="bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </button>
                                        <button type="button" @click.stop="
                                            deletedImages.push(editingItem.thumbnail); 
                                            editingItem.thumbnail_url = null;
                                        " class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full hover:scale-110 transition-all shadow-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Hidden inputs for deleted images -->
                            <template x-for="delImg in deletedImages" :key="delImg">
                                <input type="hidden" name="delete_images[]" :value="delImg">
                            </template>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-2" x-data="{ editThumbPreview: '' }">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Ganti Foto Utama (Thumbnail)</label>
                                    <div class="relative group">
                                        <input type="file" name="thumbnail" id="edit_thumbnail" class="hidden" 
                                               @change="
                                                   editFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                                   if ($event.target.files[0]) {
                                                       const reader = new FileReader();
                                                       reader.onload = (e) => { editThumbPreview = e.target.result; };
                                                       reader.readAsDataURL($event.target.files[0]);
                                                   } else {
                                                       editThumbPreview = '';
                                                   }
                                               ">
                                        <label for="edit_thumbnail" class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                            <template x-if="editThumbPreview">
                                                <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                    <img :src="editThumbPreview" class="w-full h-full object-cover">
                                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                        <p class="text-white text-xs font-bold">Ganti Foto Utama</p>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!editThumbPreview">
                                                <div class="flex flex-col items-center justify-center text-center px-4">
                                                    <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                        <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    </div>
                                                    <p class="text-sm font-bold text-gray-700" x-text="editFileName || 'Pilih foto utama baru'"></p>
                                                    <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP (Maks. 2MB)</p>
                                                </div>
                                            </template>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-2" x-data="{ editGalleryPreviews: [] }">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tambah Foto Galeri Baru</label>
                                    <div class="relative group">
                                        <input type="file" name="images[]" id="edit_images" multiple class="hidden" 
                                               @change="
                                                   editGalleryPreviews = [];
                                                   const files = $event.target.files;
                                                   for (let i = 0; i < files.length; i++) {
                                                       const reader = new FileReader();
                                                       reader.onload = (e) => { editGalleryPreviews.push(e.target.result); };
                                                       reader.readAsDataURL(files[i]);
                                                   }
                                               ">
                                        <label for="edit_images" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                            <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-700" x-text="editGalleryPreviews.length > 0 ? editGalleryPreviews.length + ' file baru dipilih' : 'Pilih foto tambahan baru'"></p>
                                            <p class="text-[10px] text-gray-400 italic mt-1">* Foto baru akan ditambahkan ke daftar galeri</p>
                                        </label>
                                    </div>
                                    
                                    <!-- Previews -->
                                    <template x-if="editGalleryPreviews.length > 0">
                                        <div class="grid grid-cols-4 gap-2 mt-2">
                                            <template x-for="(src, idx) in editGalleryPreviews" :key="idx">
                                                <div class="relative rounded-xl overflow-hidden aspect-square border border-gray-200">
                                                    <img :src="src" class="w-full h-full object-cover">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Konten</label>
                            <textarea id="edit_konten" name="konten" required rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('konten') border-red-500 @enderror"></textarea>
                            @error('konten') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Tayang</label>
                            <input type="date" id="edit_tanggal" name="tanggal_tayang" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('tanggal_tayang') border-red-500 @enderror">
                            @error('tanggal_tayang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <span class="text-sm font-medium text-gray-700">Status Aktif <span class="text-xs text-gray-500 font-normal ml-1">(Draft jika nonaktif)</span></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_status" name="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebar-hover text-white rounded-xl transition-colors text-sm font-medium shadow-lg shadow-sidebar/20">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showViewModal = false"></div>
            
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                <!-- Header -->
                <div class="flex items-center justify-between px-10 pt-8 pb-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900">Detail Berita & Promosi</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Detail Berita/Promosi</span>
                                            <p class="text-slate-200 font-normal">Halaman peninjauan detail lengkap untuk melihat bagaimana informasi, berita, atau promosi dipublikasikan kepada wisatawan.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 mt-0.5">Informasi lengkap berita dan promosi</p>
                    </div>
                    <button @click="showViewModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-10">
                    <div x-show="loading && !viewingItem" class="py-12 flex flex-col items-center justify-center gap-4">
                        <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-emerald-600 animate-pulse">Memuat data...</p>
                    </div>

                    <div x-show="viewingItem" class="space-y-8">
                        <!-- Banner Image & Gallery -->
                        <div class="space-y-3">
                            <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 aspect-video group cursor-pointer" 
                                 @click="if(viewingItem?.images_url && viewingItem.images_url.length > 0) { lightboxImage = viewingItem.images_url[activeViewImageIndex]; showLightbox = true; } else if(viewingItem?.thumbnail_url) { lightboxImage = viewingItem.thumbnail_url; showLightbox = true; }" 
                                 title="Klik untuk memperbesar">
                                <template x-if="viewingItem?.images_url && viewingItem.images_url.length > 0">
                                    <img :src="viewingItem.images_url[activeViewImageIndex]" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="">
                                </template>
                                <template x-if="!(viewingItem?.images_url && viewingItem.images_url.length > 0) && viewingItem?.thumbnail_url">
                                    <img :src="viewingItem.thumbnail_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="">
                                </template>
                                <template x-if="!(viewingItem?.images_url && viewingItem.images_url.length > 0) && !viewingItem?.thumbnail_url">
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs font-bold uppercase tracking-widest">Tidak ada foto</p>
                                    </div>
                                </template>
                                
                                <!-- Badge overlay to indicate cover vs gallery -->
                                <div class="absolute top-6 left-6" x-show="viewingItem?.images_url && viewingItem.images_url.length > 0">
                                    <span class="px-4 py-2 bg-emerald-600/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-white uppercase tracking-widest shadow-sm" x-text="activeViewImageIndex === 0 ? 'Foto Utama (Cover)' : 'Foto Tambahan (Galeri)'"></span>
                                </div>
                                
                                <div class="absolute top-6 right-6">
                                    <span :class="viewingItem?.tipe === 'PROMO' ? 'bg-orange-500/90' : 'bg-emerald-500/90'" class="px-4 py-2 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg" x-text="viewingItem?.tipe"></span>
                                </div>
                            </div>

                            <!-- Row of clickable thumbnails -->
                            <template x-if="viewingItem?.images_url && viewingItem.images_url.length > 1">
                                <div class="flex items-center gap-2 mt-3 overflow-x-auto py-1.5 custom-scrollbar">
                                    <template x-for="(imgUrl, idx) in viewingItem.images_url" :key="idx">
                                        <button type="button" @click="activeViewImageIndex = idx" 
                                                class="relative w-20 h-14 rounded-lg overflow-hidden border-2 transition-all flex-shrink-0"
                                                :class="activeViewImageIndex === idx ? 'border-emerald-600 shadow-md scale-105' : 'border-gray-200 hover:border-gray-300'">
                                            <img :src="imgUrl" class="w-full h-full object-cover">
                                            <!-- Tiny emerald triangle to flag cover -->
                                            <div x-show="idx === 0" class="absolute top-0 right-0 bg-[#066466] w-2.5 h-2.5 rounded-bl"></div>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Judul Konten</h4>
                                    <p class="text-lg font-bold text-gray-900" x-text="viewingItem?.judul || '-'"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Tanggal Tayang</h4>
                                    <p class="text-sm font-bold text-emerald-600" x-text="viewingItem?.tanggal_tayang_formatted || (viewingItem?.tanggal_tayang ? new Date(viewingItem.tanggal_tayang).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-')"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Status</h4>
                                    <template x-if="viewingItem?.is_active">
                                        <span class="inline-flex items-center gap-1.5 text-emerald-600 text-xs font-bold">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                            Terpublikasi
                                        </span>
                                    </template>
                                    <template x-if="!viewingItem?.is_active">
                                        <span class="inline-flex items-center gap-1.5 text-gray-400 text-xs font-bold">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                            Draft
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Isi Konten</h4>
                                    <div class="text-sm text-gray-600 leading-relaxed max-h-60 overflow-y-auto custom-scrollbar pr-2 whitespace-pre-line" x-text="viewingItem?.konten || 'Tidak ada konten.'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-10 py-6 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-[10px] font-bold text-emerald-700" x-text="viewingItem?.admin?.name ? viewingItem.admin.name.split(' ').map(n => n[0]).join('').substring(0,2) : 'A'"></div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Diterbitkan Oleh</p>
                            <p class="text-xs font-bold text-gray-700" x-text="viewingItem?.admin?.name || 'Administrator'"></p>
                        </div>
                    </div>
                    <button @click="showViewModal = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all shadow-sm">Tutup</button>
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

</div>
@endsection

