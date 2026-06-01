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
<button @click="$dispatch('open-create-modal')" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
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

    async openViewModal(id) {
        if (!id) return;
        this.loading = true;
        this.showViewModal = true;
        this.viewingItem = null;
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
    <div class="mb-6"></div>

    <!-- Filters -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
        <form action="{{ route('admin.berita_promosi.index') }}" method="GET" class="flex flex-wrap w-full gap-4 items-center" id="filter-form">
            <div class="flex-1 min-w-[300px] relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita atau promosi..." class="w-full pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl outline-none text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            
            <select name="tipe" class="px-6 py-3 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium" onchange="document.getElementById('filter-form').submit()">
                <option value="">Semua Tipe</option>
                <option value="BERITA" {{ request('tipe') == 'BERITA' ? 'selected' : '' }}>Berita</option>
                <option value="PROMO" {{ request('tipe') == 'PROMO' ? 'selected' : '' }}>Promo</option>
            </select>
            
            <select name="status" class="px-6 py-3 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium" onchange="document.getElementById('filter-form').submit()">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>

            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'tanggal_tayang') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
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
                                    <img src="{{ image_url($item->thumbnail) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-gray-100 group-hover:scale-105 transition-transform" alt="">
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

    <!-- Tambah Modal -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Berita/Promosi</h3>
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
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Thumbnail</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors relative overflow-hidden">
                                <template x-if="createPreviewUrl">
                                    <img :src="createPreviewUrl" class="absolute inset-0 w-full h-full object-cover opacity-20 pointer-events-none">
                                </template>
                                <input type="file" name="thumbnail" required accept="image/png, image/jpeg, image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    @change="
                                        createFileName = $event.target.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => { createPreviewUrl = e.target.result };
                                        reader.readAsDataURL($event.target.files[0]);
                                    ">
                                <svg class="w-8 h-8 text-sidebar mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <p class="text-sm text-gray-700 font-medium" x-text="createFileName || 'Klik atau seret foto ke sini'"></p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG (Maks. 2MB, Rekomendasi 1920x1080px)</p>
                            </div>
                            @error('thumbnail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false"></div>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Edit Berita/Promosi</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
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
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Thumbnail</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors relative overflow-hidden">
                                <template x-if="editPreviewUrl">
                                    <img :src="editPreviewUrl" class="absolute inset-0 w-full h-full object-cover opacity-20 pointer-events-none">
                                </template>
                                <input type="file" name="thumbnail" accept="image/png, image/jpeg, image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    @change="
                                        editFileName = $event.target.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => { editPreviewUrl = e.target.result };
                                        reader.readAsDataURL($event.target.files[0]);
                                    ">
                                <svg class="w-8 h-8 text-sidebar mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <p class="text-sm text-gray-700 font-medium" x-text="editFileName || 'Klik untuk ganti foto'"></p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG (Maks. 2MB, Rekomendasi 1920x1080px)</p>
                            </div>
                            @error('thumbnail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                        <h3 class="text-xl font-bold text-gray-900">Detail Konten</h3>
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
                        <!-- Banner Image -->
                        <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 aspect-video group">
                            <template x-if="viewingItem?.thumbnail_url">
                                <img :src="viewingItem.thumbnail_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="">
                            </template>
                            <template x-if="!viewingItem?.thumbnail_url">
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-xs font-bold uppercase tracking-widest">Tidak ada foto</p>
                                </div>
                            </template>
                            <div class="absolute top-6 right-6">
                                <span :class="viewingItem?.tipe === 'PROMO' ? 'bg-orange-500' : 'bg-emerald-500'" class="px-4 py-2 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg" x-text="viewingItem?.tipe"></span>
                            </div>
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


</div>
@endsection

@push('scripts')
<script>
    // Global helper for opening the create modal from page_actions
    window.addEventListener('DOMContentLoaded', () => {
        // Find the button in the page_actions section
        const createBtn = document.querySelector('button[class*=\"bg-sidebar\"][class*=\"text-white\"]:not([type=\"submit\"])');
        if (createBtn) {
            createBtn.removeAttribute('onclick');
            createBtn.addEventListener('click', () => {
                const alpineEl = document.querySelector('[x-data]');
                if (alpineEl && alpineEl.__x) {
                    alpineEl.__x.$data.showCreateModal = true;
                }
            });
        }
    });
</script>
@endpush
