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

@section('title', 'Kelola Destinasi')
@section('navbar_title', 'Destinasi')
@section('page_title', 'Destinasi')
@section('page_description', 'Kelola konten destinasi wisata Danau Toba')

@section('page_actions')
<button @click="$dispatch('open-create-modal')" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
    Tambah Destinasi
</button>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Destinasi</span>
</nav>
@endsection

@section('content')
<div id="dest-manager" x-data="{
    activeTab: '{{ $activeTab }}',
    showCreateModal: false,
    showEditModal: false,
    editingDest: null,
    loading: false,
    createFileName: '',
    editFileName: '',
    openTime: '08:00',
    closeTime: '17:00',
    editOpenTime: '08:00',
    editCloseTime: '17:00',
    showViewModal: false,
    viewingDest: null,

    // Tab switcher
    switchTab(tab) {
        this.activeTab = tab;
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
        if (tab === 'trending') {
             // Re-fetch trending data if needed, or simply reload if logic is server-side
             if (!{{ isset($trendingDestinations) ? 'true' : 'false' }}) {
                 window.location.href = url.toString();
             }
        }
    },

    async openEditModal(id) {
        this.loading = true;
        this.showEditModal = true;
        this.editingDest = null;
        try {
            const res = await fetch(`/admin/destinations/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.editingDest = await window.safeParseJSON(res);
            this.editFileName = this.editingDest.images && this.editingDest.images.length ? 'Foto saat ini' : '';
            
            const form = document.getElementById('editDestForm');
            if (form) form.action = `/admin/destinations/${id}`;

            if (this.editingDest.opening_hours && this.editingDest.opening_hours.includes(' - ')) {
                const parts = this.editingDest.opening_hours.split(' - ');
                this.editOpenTime = parts[0];
                this.editCloseTime = parts[1];
            } else {
                this.editOpenTime = '08:00';
                this.editCloseTime = '17:00';
            }
        } catch(e) {
            alert('Gagal mengambil data destinasi');
            this.showEditModal = false;
        } finally {
            this.loading = false;
        }
    },

    async openViewModal(id) {
        this.loading = true;
        this.showViewModal = true;
        this.viewingDest = null;
        try {
            const res = await fetch(`/admin/destinations/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.viewingDest = await window.safeParseJSON(res);
        } catch(e) {
            alert('Gagal mengambil data destinasi');
            this.showViewModal = false;
        } finally {
            this.loading = false;
        }
    },

    async submitEdit() {
        this.loading = true;
        const form = document.getElementById('editDestForm');
        const formData = new FormData(form);
        const destId = this.editingDest._id || this.editingDest.id;
        try {
            const res = await fetch(`/admin/destinations/${destId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: formData
            });
            const result = await window.safeParseJSON(res);
            if (result.success) { window.location.reload(); }
            else { alert(result.message || 'Gagal menyimpan'); }
        } catch(e) {
            alert('Terjadi kesalahan');
        } finally { this.loading = false; }
    }
}" @open-create-modal.window="showCreateModal = true">

    <!-- Tab Navigation -->
    <div class="flex items-center gap-1 bg-gray-100/50 p-1 rounded-2xl w-fit mb-8 border border-gray-200/50">
        <button @click="switchTab('manage')" 
            :class="activeTab === 'manage' ? 'bg-white text-sidebar shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700'"
            class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all border border-transparent">
            Kelola Destinasi
        </button>
        <button @click="switchTab('trending')" 
            :class="activeTab === 'trending' ? 'bg-white text-sidebar shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700'"
            class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all border border-transparent">
            Trending & Analisis
        </button>
    </div>

    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true"></button>

    {{-- TAB 1: MANAGE DESTINATIONS --}}
    <div x-show="activeTab === 'manage'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        {{-- Search & Filter --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
            <form method="GET" action="{{ route('admin.destinations.index') }}" class="flex flex-wrap items-center gap-4 w-full">
            <!-- Persist current sorting -->
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="relative flex-1 min-w-[280px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau deskripsi..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-300">
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[13px] font-bold text-gray-400">Tampilkan:</span>
                <select name="per_page" onchange="this.form.submit()" 
                    class="px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-emerald-500 transition-all cursor-pointer">
                    @foreach([10, 20, 50, 100] as $val)
                        <option value="{{ $val }}" @selected(request('per_page', 10) == $val)>{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <select name="category" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-emerald-500 transition-all cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach(($categories ?? []) as $cat)
                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-emerald-500 transition-all cursor-pointer">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    <tr class="bg-white border-b border-gray-50">
                        @php
                            $sortOrder = request('sort_order') === 'asc' ? 'desc' : 'asc';
                            $currentSort = request('sort_by', 'created_at');
                        @endphp
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($currentSort === 'name' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Destinasi
                                <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'name' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_order' => ($currentSort === 'category' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Kategori
                                <svg class="w-4 h-4 {{ $currentSort === 'category' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'category' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'average_rating', 'sort_order' => ($currentSort === 'average_rating' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Rating
                                <svg class="w-4 h-4 {{ $currentSort === 'average_rating' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'average_rating' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_active', 'sort_order' => ($currentSort === 'is_active' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Status
                                <svg class="w-4 h-4 {{ $currentSort === 'is_active' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'is_active' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($currentSort === 'created_at' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Dibuat
                                <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'created_at' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse(($destinations ?? []) as $index => $destination)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-8 py-5 text-sm font-semibold text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    @if(isset($destination->images) && count($destination->images) > 0)
                                        <img src="{{ image_url($destination->images[0]) }}" alt="{{ $destination->name }}" class="w-24 h-16 object-cover rounded-xl shadow-sm border border-gray-100">
                                    @else
                                        <div class="w-24 h-16 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="text-[15px] font-bold text-gray-800 max-w-[200px] truncate" title="{{ $destination->name ?? '' }}">{{ $destination->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5 max-w-[150px] truncate" title="{{ $destination->location ?? '' }}">{{ $destination->location ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="font-bold text-xs text-[#066466]">{{ ucfirst($destination->category ?? '-') }}</span>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    <span class="text-sm font-bold text-gray-700">{{ number_format($destination->average_rating ?? 0, 1) }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                @if($destination->is_active ?? false)
                                    <span class="px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] rounded-xl font-bold text-xs">Aktif</span>
                                @else
                                    <span class="px-4 py-1.5 bg-gray-100 text-gray-400 rounded-xl font-bold text-xs">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-10 py-6">
                                <div class="text-[13px] text-gray-500 font-medium">{{ $destination->created_at?->format('d M Y') ?? '-' }}</div>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openViewModal('{{ $destination->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button @click="openEditModal('{{ $destination->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.destinations.destroy', $destination->_id) }}', title: 'Hapus Destinasi', type: 'destinasi', name: {{ json_encode($destination->name) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-14 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <p class="text-sm font-medium">Tidak ada destinasi ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($destinations) && method_exists($destinations, 'links'))
    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
        <div class="text-gray-400 text-sm font-medium">Menampilkan {{ $destinations->count() }} dari {{ $destinations->total() }} Destinasi</div>
        <div>{{ $destinations->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
    </div>
    @endif
    </div>

    {{-- TAB 2: TRENDING & ANALYTICS --}}
    @if(isset($trendingDestinations))
    <div x-show="activeTab === 'trending'" x-data="trendingManager()" x-init="init()" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="pb-10">
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Pencarian</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_search']) }}</h3>
                    <p class="text-xs text-green-500 font-bold mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5H7z"/></svg>
                        +{{ $stats['search_increase'] }}% minggu ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Wishlist Tertambah</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_wishlist']) }}</h3>
                    <p class="text-xs text-green-500 font-bold mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5H7z"/></svg>
                        +{{ $stats['wishlist_increase'] }}% minggu ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Ulasan</p>
                    <h3 class="text-3xl font-bold text-orange-500">{{ number_format($stats['total_review']) }}</h3>
                    <p class="text-xs text-green-500 font-bold mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5H7z"/></svg>
                        +{{ $stats['review_increase'] }}% minggu ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Tren Pencarian Destinasi — 7 Hari Terakhir</h3>
            <div class="h-80 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Configuration & Management -->
        <div class="bg-teal-50/40 p-6 rounded-[2rem] border border-teal-100 mb-8">
            <div class="flex items-center gap-4 justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-teal-100 text-teal-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-teal-900 text-sm">Mode Trending Aktif</h4>
                        <p class="text-xs text-teal-700">Tentukan bagaimana destinasi trending muncul di aplikasi mobile.</p>
                    </div>
                </div>
                <div class="flex bg-white p-1 rounded-xl shadow-sm border border-teal-100">
                    <button @click="setMode('manual')" :class="mode === 'manual' ? 'bg-sidebar text-white shadow-md' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[10px] font-bold transition-all">Manual</button>
                    <button @click="setMode('automatic')" :class="mode === 'automatic' ? 'bg-sidebar text-white shadow-md' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[10px] font-bold transition-all">Otomatis</button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Urutan Trending</h3>
                        <p class="text-xs text-gray-400 mt-1" x-show="mode === 'manual'">Drag & drop untuk mengubah urutan</p>
                    </div>
                    <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                        <span x-text="trendingList.length"></span>/10 Destinasi
                    </span>
                </div>

                <div class="space-y-3" id="trending-sortable">
                    <template x-for="(item, index) in trendingList" :key="item.id_str">
                        <div class="flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-2xl hover:shadow-md transition-all group" :data-id="item.id_str">
                            <div class="text-gray-300 hover:text-gray-500 drag-handle" :class="mode === 'manual' ? 'cursor-grab' : 'opacity-50 cursor-not-allowed'" x-show="mode === 'manual'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-sidebar flex items-center justify-center text-white text-[10px] font-bold" x-text="index + 1"></div>
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100">
                                <img :src="item.images && item.images[0] ? (item.images[0].startsWith('http') ? item.images[0] : '/storage/' + item.images[0]) : ''" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 text-sm" x-text="item.name"></h4>
                                <p class="text-[10px] text-gray-400 capitalize" x-text="item.category"></p>
                            </div>
                            <button x-show="mode === 'manual'" @click="removeItem(item.id_str)" class="p-2 text-red-400 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-50" x-show="mode === 'manual'">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchTrendingDestinations()" placeholder="Tambah destinasi ke trending..." class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-sidebar/10">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        
                        <div x-show="searchResults.length > 0" class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                            <template x-for="res in searchResults" :key="res.id_str">
                                <div @click="addItem(res)" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0">
                                        <img :src="res.images && res.images[0] ? (res.images[0].startsWith('http') ? res.images[0] : '/storage/' + res.images[0]) : ''" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-800 text-sm" x-text="res.name"></p>
                                        <p class="text-[10px] text-gray-400 truncate" x-text="res.location"></p>
                                    </div>
                                    <svg class="w-4 h-4 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-4" x-show="mode === 'manual'">
                    <button @click="saveOrder()" class="flex-1 py-4 bg-sidebar text-white rounded-2xl font-bold shadow-lg shadow-sidebar/20 hover:opacity-95 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Urutan
                    </button>
                </div>
            </div>

            <!-- Preview Mobile -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 sticky top-8">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Pratinjau Mobile</h3>
                <div class="relative mx-auto w-[220px] h-[440px] bg-white border-[6px] border-gray-800 rounded-[2.5rem] shadow-2xl overflow-hidden">
                    <div class="absolute top-0 inset-x-0 h-4 bg-gray-800 rounded-b-xl w-20 mx-auto z-20"></div>
                    <div class="h-full bg-gray-50 pt-8 px-3 overflow-hidden">
                        <h4 class="text-[10px] font-bold text-gray-900 mb-3 uppercase tracking-wider">Trending</h4>
                        <div class="space-y-3">
                            <template x-for="(item, i) in trendingList.slice(0, 4)" :key="item.id_str">
                                <div class="relative w-full h-24 rounded-xl overflow-hidden shadow-sm bg-gray-200">
                                    <img :src="item.images && item.images[0] ? (item.images[0].startsWith('http') ? item.images[0] : '/storage/' + item.images[0]) : ''" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                    <div class="absolute bottom-2 left-2 pr-2">
                                        <h5 class="text-white font-bold text-[9px] leading-tight truncate" x-text="item.name"></h5>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <svg class="w-2 h-2 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            <span class="text-[7px] text-white/80" x-text="item.average_rating || '0.0'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal for Trending -->
        <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center">
                <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2" x-text="modalTitle"></h3>
                <p class="text-gray-500 mb-8" x-text="successMessage"></p>
                <button @click="showSuccessModal = false" class="w-full py-4 bg-sidebar text-white rounded-2xl font-bold shadow-lg hover:opacity-95 transition-all">Selesai</button>
            </div>
        </div>
    </div>
    @endif

    {{-- CREATE MODAL --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showCreateModal = false"></div>

              <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                  class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Destinasi</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Destinasi</label>
                            <input type="text" name="name" required placeholder="Festival Danau Toba" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                            <select name="category" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                @foreach(($categories ?? []) as $cat)<option value="{{ $cat }}">{{ ucfirst($cat) }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi</label>
                            <input type="text" name="location" required placeholder="Balige, Toba" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Latitude</label>
                            <input type="text" name="latitude" id="create_latitude" required placeholder="2.6845" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                            <input type="text" name="longitude" id="create_longitude" required placeholder="98.8756" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        {{-- Map Picker for Create --}}
                        <div class="col-span-2 space-y-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Destinasi (Klik/Geser untuk mengubah)</label>
                            
                            {{-- Search Box --}}
                            <div class="flex gap-2">
                                <div class="relative flex-1 group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" id="create_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all" autocomplete="off">
                                    <button type="button" onclick="performSearch('create_location_search', 'create_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-xl hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </button>
                                </div>
                                <button type="button" onclick="getCurrentLocation('create_latitude', 'create_longitude', 'create_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-xs font-bold hidden sm:inline">Lokasi Saya</span>
                                </button>
                            </div>

                            <div id="create_map_picker" style="width: 100%; height: 300px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                            <p class="text-[10px] text-gray-400 italic">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Fasilitas (Pisahkan dengan koma)</label>
                            <input type="text" name="facilities" placeholder="Toko Suvenir, Toilet Umum, Area Parkir" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        {{-- Jam Operasional, Tiket, Best Time (Fixed layout) --}}
                        <div class="col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                <div class="flex items-center gap-2">
                                    <input type="time" x-model="openTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    <span class="text-gray-400">-</span>
                                    <input type="time" x-model="closeTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                </div>
                                <input type="hidden" name="opening_hours" :value="openTime + ' - ' + closeTime">
                            </div>
                            <div class="space-y-2 col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                <input type="text" name="ticket_price" value="Gratis" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2 col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                <input type="text" name="best_time" value="Kapan saja" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                            </div>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                            <textarea name="description" rows="3" required placeholder="Deskripsi singkat destinasi..." class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 placeholder-gray-300"></textarea>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Foto Utama (Thumbnail)</label>
                            <div class="relative group">
                                <input type="file" name="thumbnail" id="create_thumbnail" required class="absolute inset-0 opacity-0 cursor-pointer" @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                <label for="create_thumbnail" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                    <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Klik untuk upload foto'"></p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tight">PNG, JPG, WEBP (Maks. 5MB)</p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Batal</button>
                        <button type="submit" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all">Simpan Destinasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showEditModal = false"></div>

              <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                  class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Edit Destinasi</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !editingDest" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div x-show="editingDest">
                    <form id="editDestForm" @submit.prevent="submitEdit()" class="space-y-5">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Destinasi</label>
                                <input type="text" name="name" x-model="editingDest.name" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                                <select name="category" x-model="editingDest.category" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    @foreach(($categories ?? []) as $cat)<option value="{{ $cat }}">{{ ucfirst($cat) }}</option>@endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi</label>
                                <input type="text" name="location" x-model="editingDest.location" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Latitude</label>
                                <input type="text" name="latitude" id="edit_latitude" x-model="editingDest.latitude" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                                <input type="text" name="longitude" id="edit_longitude" x-model="editingDest.longitude" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            {{-- Map Picker for Edit --}}
                            <div class="col-span-2 space-y-3">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Destinasi (Klik/Geser untuk mengubah)</label>
                                
                                {{-- Search Box --}}
                                <div class="flex gap-2">
                                    <div class="relative flex-1 group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <input type="text" id="edit_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all" autocomplete="off">
                                        <button type="button" onclick="performSearch('edit_location_search', 'edit_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-lg hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </button>
                                    </div>
                                    <button type="button" onclick="getCurrentLocation('edit_latitude', 'edit_longitude', 'edit_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span class="text-xs font-bold hidden sm:inline">Lokasi Saya</span>
                                    </button>
                                </div>

                                <div id="edit_map_picker" style="width: 100%; height: 300px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                                <p class="text-[10px] text-gray-400 italic">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Fasilitas (Pisahkan dengan koma)</label>
                                <input type="text" name="facilities"
                                    :value="(() => {
                                        const f = editingDest.facilities;
                                        if (!f) return '';
                                        if (Array.isArray(f)) return f.join(', ');
                                        if (typeof f === 'string') {
                                            try {
                                                const parsed = JSON.parse(f);
                                                if (Array.isArray(parsed)) return parsed.join(', ');
                                            } catch(e) {}
                                            return f.replace(/^\[|\]$/g, '').replace(/\"/g, '');
                                        }
                                        return '';
                                    })()"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            {{-- Jam Operasional, Tiket, Best Time (Fixed layout) --}}
                            <div class="col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4" x-show="editingDest">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" x-model="editOpenTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" x-model="editCloseTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    </div>
                                    <input type="hidden" name="opening_hours" :value="editOpenTime + ' - ' + editCloseTime">
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" x-model="editingDest.ticket_price" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" x-model="editingDest.best_time" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                </div>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                                <textarea name="description" rows="3" x-model="editingDest.description" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700"></textarea>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Ganti Foto Utama</label>
                                <div x-show="editingDest && editingDest.images && editingDest.images.length > 0" class="mb-3">
                                    <img :src="editingDest.images[0].startsWith('http') ? editingDest.images[0] : `/storage/${editingDest.images[0]}`" class="w-full h-40 object-cover rounded-2xl shadow-sm border border-gray-100">
                                    <p class="text-[10px] text-gray-400 mt-1">Foto saat ini</p>
                                </div>
                                <div class="relative group">
                                    <input type="file" name="thumbnail" id="edit_thumbnail" class="absolute inset-0 opacity-0 cursor-pointer" @change="editFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                    <label for="edit_thumbnail" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                        <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700" x-text="editFileName || 'Klik untuk ganti foto'"></p>
                                        <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP (Maks. 5MB)</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="showEditModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Batal</button>
                            <button type="submit" :disabled="loading" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL DESTINATION MODAL --}}
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
                        <h3 class="text-xl font-bold text-gray-900">Detail Destinasi</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Informasi lengkap destinasi wisata</p>
                    </div>
                    <button @click="showViewModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-10">
                    <div x-show="loading && !viewingDest" class="py-12 flex flex-col items-center justify-center gap-4">
                        <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-emerald-600 animate-pulse">Memuat data...</p>
                    </div>

                    <div x-show="viewingDest" class="space-y-8">
                        <!-- Image Gallery (Main Image) -->
                        <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 aspect-video group">
                            <template x-if="viewingDest?.images && viewingDest.images.length > 0">
                                <img :src="viewingDest.images[0].startsWith('http') ? viewingDest.images[0] : '/storage/' + viewingDest.images[0]" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="">
                            </template>
                            <template x-if="!viewingDest?.images || viewingDest.images.length === 0">
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-xs font-bold uppercase tracking-widest">Tidak ada foto</p>
                                </div>
                            </template>
                            <div class="absolute top-6 right-6">
                                <span class="px-4 py-2 bg-white/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-gray-900 uppercase tracking-widest shadow-sm" x-text="viewingDest?.category || '-'"></span>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Destinasi</h4>
                                    <p class="text-lg font-bold text-gray-900" x-text="viewingDest?.name || '-'"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Lokasi / Alamat</h4>
                                    <p class="text-sm font-medium text-gray-600 leading-relaxed" x-text="viewingDest?.location || '-'"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Fasilitas</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-if="(() => {
                                            const f = viewingDest?.facilities;
                                            if (!f) return false;
                                            if (Array.isArray(f)) return f.length > 0;
                                            if (typeof f === 'string') {
                                                const cleaned = f.replace(/^\[|\]$/g, '').trim();
                                                return cleaned.length > 0;
                                            }
                                            return false;
                                        })()">
                                            <template x-for="fac in (() => {
                                                const f = viewingDest.facilities;
                                                if (!f) return [];
                                                if (Array.isArray(f)) return f;
                                                if (typeof f === 'string') {
                                                    try {
                                                        const parsed = JSON.parse(f);
                                                        if (Array.isArray(parsed)) return parsed;
                                                    } catch(e) {}
                                                    return f.replace(/^\[|\]$/g, '').split(',').map(s => s.replace(/\"/g, '').trim()).filter(s => s);
                                                }
                                                return [];
                                            })()" :key="fac">
                                                <span class="px-3 py-1.5 bg-sidebar-active/10 text-sidebar-active rounded-xl text-xs font-semibold" x-text="fac"></span>
                                            </template>
                                        </template>
                                        <template x-if="(() => {
                                            const f = viewingDest?.facilities;
                                            if (!f) return true;
                                            if (Array.isArray(f)) return f.length === 0;
                                            if (typeof f === 'string') {
                                                const cleaned = f.replace(/^\[|\]$/g, '').trim();
                                                return cleaned.length === 0;
                                            }
                                            return true;
                                        })()">
                                            <span class="text-sm font-semibold text-gray-500">-</span>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center gap-8">
                                    <div>
                                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Jam Buka</h4>
                                        <p class="text-sm font-bold text-emerald-600" x-text="viewingDest?.opening_hours || '-'"></p>
                                    </div>
                                    <div>
                                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Rating</h4>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <span class="text-sm font-bold text-gray-900" x-text="viewingDest?.rating || '0'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Deskripsi</h4>
                                    <div class="text-sm text-gray-500 leading-relaxed line-clamp-6 custom-scrollbar pr-2" x-text="viewingDest?.description || 'Tidak ada deskripsi.'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Map Preview -->
                        <div class="space-y-3 pt-4 border-t border-gray-50">
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Kordinat Geografis</h4>
                            <div class="flex items-center gap-4 text-xs font-mono text-gray-500 bg-gray-50 p-4 rounded-2xl">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-400">LAT:</span>
                                    <span x-text="viewingDest?.latitude || '-'"></span>
                                </div>
                                <div class="flex items-center gap-2 border-l border-gray-200 pl-4">
                                    <span class="font-bold text-gray-400">LNG:</span>
                                    <span x-text="viewingDest?.longitude || '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-10 py-6 bg-gray-50 flex items-center justify-between">
                    <p class="text-xs text-gray-400 font-medium italic">Terakhir diperbarui: <span x-text="viewingDest?.updated_at ? new Date(viewingDest.updated_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-'"></span></p>
                    <button @click="showViewModal = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=Function.prototype" async defer></script>
<script>
    // Handler untuk error autentikasi Google Maps
    window.gm_authFailure = function() {
        console.error('Google Maps authentication failed! Periksa API Key Anda.');
        const pickers = document.querySelectorAll('[id*="map_picker"]');
        pickers.forEach(p => {
            p.innerHTML = '<div class="flex items-center justify-center h-full text-red-500 text-xs font-bold p-4 text-center">Google Maps Error: API Key tidak valid atau belum diaktifkan.</div>';
        });
    };

    let createMap, editMap, createMarker, editMarker;

    function initGoogleMap(elementId, latId, lngId, initialPos = { lat: 2.3361, lng: 99.0631 }) {
        if (typeof google === 'undefined') {
            console.warn('Google Maps API not yet loaded');
            return null;
        }

        const mapElement = document.getElementById(elementId);
        if (!mapElement) return null;

        const map = new google.maps.Map(mapElement, {
            zoom: 13,
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

        const latInput = document.getElementById(latId);
        const lngInput = document.getElementById(lngId);
        if (latInput && lngInput) {
            const updateFromInput = () => {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    const pos = { lat, lng };
                    marker.setPosition(pos);
                    map.setCenter(pos);
                }
            };
            latInput.addEventListener('change', updateFromInput);
            lngInput.addEventListener('change', updateFromInput);
        }

        // --- Location Search Feature using standard Autocomplete ---
        const searchInputId = elementId.includes('create') ? 'create_location_search' : 'edit_location_search';
        const searchInput = document.getElementById(searchInputId);

        if (searchInput && typeof google.maps.places.Autocomplete !== 'undefined') {
            const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                componentRestrictions: { country: 'id' },
                fields: ['geometry', 'formatted_address', 'name'],
                types: ['geocode', 'establishment']
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    console.warn("No geometry for place: " + place.name);
                    return;
                }

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
                    performSearch(searchInputId, elementId);
                }
            });
        }
        // --- End Search Feature ---

        // Ensure map renders correctly
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

    // --- Trending Manager Alpine Data ---
    function trendingManager() {
        return {
            mode: '{{ $mode ?? 'manual' }}',
            trendingList: @json($trendingDestinations ?? []),
            searchQuery: '',
            searchResults: [],
            showSuccessModal: false,
            modalTitle: '',
            successMessage: '',

            init() {
                this.initChart();
                if (this.mode === 'manual') {
                    this.initSortable();
                }
            },

            initChart() {
                const ctx = document.getElementById('trendChart')?.getContext('2d');
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Pencarian',
                            data: [420, 580, 490, 720, 850, 1100, 980],
                            borderColor: '#066466',
                            backgroundColor: 'rgba(6, 100, 102, 0.05)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#066466',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f0f0f0' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            },

            initSortable() {
                const el = document.getElementById('trending-sortable');
                if (!el) return;
                
                Sortable.create(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'bg-teal-50',
                    onEnd: () => {
                        const newOrder = Array.from(el.querySelectorAll('[data-id]'))
                            .map(item => item.getAttribute('data-id'));
                        
                        const newList = [];
                        newOrder.forEach(id => {
                            const item = this.trendingList.find(i => i.id_str === id);
                            if (item) newList.push(item);
                        });
                        this.trendingList = newList;
                    }
                });
            },

            async setMode(newMode) {
                if (this.mode === newMode) return;
                try {
                    const res = await fetch('{{ route('admin.trending.update-mode') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ mode: newMode })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.mode = newMode;
                        this.showSuccess('Berhasil!', `Mode trending diubah ke ${newMode}`);
                        if (newMode === 'manual') setTimeout(() => this.initSortable(), 500);
                        else window.location.reload();
                    }
                } catch(e) { alert('Gagal mengubah mode'); }
            },

            async searchTrendingDestinations() {
                if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                try {
                    const res = await fetch(`{{ route('admin.trending.search') }}?q=${this.searchQuery}`);
                    this.searchResults = await res.json();
                } catch(e) { console.error(e); }
            },

            async addItem(item) {
                if (this.trendingList.length >= 10) { alert('Maksimal 10 destinasi trending'); return; }
                if (this.trendingList.find(i => i.id_str === item.id_str)) { alert('Destinasi sudah ada di daftar'); return; }

                try {
                    const res = await fetch('{{ route('admin.trending.add') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ destination_id: item.id_str })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.trendingList.push(item);
                        this.searchQuery = '';
                        this.searchResults = [];
                        this.showSuccess('Ditambahkan!', 'Destinasi berhasil masuk daftar trending');
                    }
                } catch(e) { alert('Gagal menambahkan'); }
            },

            async removeItem(id) {
                if (!confirm('Hapus dari trending?')) return;
                try {
                    const res = await fetch(`/admin/trending-destinations/remove/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.trendingList = this.trendingList.filter(i => i.id_str !== id);
                        this.showSuccess('Dihapus!', 'Destinasi dikeluarkan dari trending');
                    }
                } catch(e) { alert('Gagal menghapus'); }
            },

            async saveOrder() {
                const orders = this.trendingList.map(i => i.id_str);
                try {
                    const res = await fetch('{{ route('admin.trending.update-order') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ orders })
                    });
                    const data = await res.json();
                    if (data.success) this.showSuccess('Tersimpan!', 'Urutan trending berhasil diperbarui');
                } catch(e) { alert('Gagal menyimpan urutan'); }
            },

            showSuccess(title, msg) {
                this.modalTitle = title;
                this.successMessage = msg;
                this.showSuccessModal = true;
            }
        };
    }
    setInterval(() => {
        const el = document.getElementById('dest-manager');
        if (el && window.Alpine) {
            const data = Alpine.$data(el);
            
            if (data) {
                // Initialize Create Map
                if (data.showCreateModal && !createMap && typeof google !== 'undefined') {
                    createMap = true; // Temporary flag
                    setTimeout(() => {
                        const res = initGoogleMap('create_map_picker', 'create_latitude', 'create_longitude');
                        if(res) {
                            createMap = res.map;
                            createMarker = res.marker;
                        } else { createMap = null; }
                    }, 500); // Wait for modal animation
                } else if (!data.showCreateModal) {
                    createMap = null;
                }

                // Initialize Edit Map
                if (data.showEditModal && data.editingDest && !editMap && typeof google !== 'undefined') {
                    editMap = true; // Temporary flag
                    setTimeout(() => {
                        const pos = { 
                            lat: parseFloat(data.editingDest.latitude) || 2.3361, 
                            lng: parseFloat(data.editingDest.longitude) || 99.0631 
                        };
                        const res = initGoogleMap('edit_map_picker', 'edit_latitude', 'edit_longitude', pos);
                        if(res) {
                            editMap = res.map;
                            editMarker = res.marker;
                        } else { editMap = null; }
                    }, 500); // Wait for modal animation
                } else if (!data.showEditModal) {
                    editMap = null;
                }
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

                // Update Map & Marker if they exist
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
@endpush
