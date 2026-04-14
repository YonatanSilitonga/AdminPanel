@extends('admin.layouts.app')

@section('title', 'Kelola Destinasi')
@section('page_title', 'Destinasi')
@section('page_description', 'Kelola konten destinasi wisata Danau Toba')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="#" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <a href="#" class="hover:text-sidebar transition-colors">Content Management</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Kelola Destinasi</span>
</nav>
@endsection

@section('content')
<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    editingDest: null,
    loading: false,
    createFileName: '',
    editFileName: '',

    async openEditModal(id) {
        this.loading = true;
        this.showEditModal = true;
        this.editingDest = null;
        try {
            const res = await fetch(`/admin/destinations/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.editingDest = await res.json();
            this.editFileName = this.editingDest.images && this.editingDest.images.length ? 'Foto saat ini' : '';
        } catch(e) {
            alert('Gagal mengambil data destinasi');
            this.showEditModal = false;
        } finally {
            this.loading = false;
        }
    },

    async submitEdit() {
        this.loading = true;
        const form = document.getElementById('editDestForm');
        const formData = new FormData(form);
        try {
            const res = await fetch(`/admin/destinations/${this.editingDest._id}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: formData
            });
            const result = await res.json();
            if (result.success) { window.location.reload(); }
            else { alert(result.message || 'Gagal menyimpan'); }
        } catch(e) {
            alert('Terjadi kesalahan');
        } finally { this.loading = false; }
    }
}">

    {{-- Search & Filter --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <form method="GET" action="{{ route('admin.destinations.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="relative w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau deskripsi..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-300">
            </div>
            <select name="category" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium">
                <option value="">Semua Kategori</option>
                @foreach(($categories ?? []) as $cat)
                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
        </form>

        <button @click="showCreateModal = true" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Destinasi
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    <tr>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Destinasi</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Featured</th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse(($destinations ?? []) as $destination)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    @if(isset($destination->images) && count($destination->images) > 0)
                                        <img src="{{ asset('storage/' . $destination->images[0]) }}" alt="{{ $destination->name }}" class="w-14 h-10 object-cover rounded-xl shadow-sm border border-gray-100">
                                    @else
                                        <div class="w-14 h-10 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-[15px] font-bold text-gray-800">{{ $destination->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $destination->location ?? '-' }}</div>
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
                                <form action="{{ route('admin.destinations.toggle-status', $destination->_id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="{{ ($destination->is_active ?? false) ? 'bg-[#E6F6F2] text-[#00A884]' : 'bg-gray-100 text-gray-400' }} px-4 py-1.5 rounded-xl font-bold text-xs inline-block">
                                        {{ ($destination->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-10 py-6">
                                <form action="{{ route('admin.destinations.toggle-featured', $destination->_id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="{{ ($destination->is_featured ?? false) ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400' }} px-4 py-1.5 rounded-xl font-bold text-xs inline-block">
                                        {{ ($destination->is_featured ?? false) ? 'Featured' : 'Biasa' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openEditModal('{{ $destination->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all">
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

    {{-- CREATE MODAL --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/20 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10 max-h-[90vh] overflow-y-auto">

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
                            <input type="text" name="latitude" required placeholder="2.6845" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                            <input type="text" name="longitude" required placeholder="98.8756" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                            <textarea name="description" rows="3" required placeholder="Deskripsi singkat destinasi..." class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 placeholder-gray-300"></textarea>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Foto Utama (Thumbnail)</label>
                            <div class="relative group">
                                <input type="file" name="thumbnail" id="create_thumbnail" required class="hidden" @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
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
                 class="fixed inset-0 bg-gray-500/20 backdrop-blur-sm" @click="showEditModal = false"></div>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10 max-h-[90vh] overflow-y-auto">

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
                                <input type="text" name="latitude" x-model="editingDest.latitude" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                                <input type="text" name="longitude" x-model="editingDest.longitude" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                                <textarea name="description" rows="3" x-model="editingDest.description" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700"></textarea>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Ganti Foto Utama</label>
                                <div x-show="editingDest && editingDest.images && editingDest.images.length > 0" class="mb-3">
                                    <img :src="`/storage/${editingDest && editingDest.images ? editingDest.images[0] : ''}`" class="w-full h-40 object-cover rounded-2xl shadow-sm border border-gray-100">
                                    <p class="text-[10px] text-gray-400 mt-1">Foto saat ini</p>
                                </div>
                                <div class="relative group">
                                    <input type="file" name="thumbnail" id="edit_thumbnail" class="hidden" @change="editFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
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
</div>
@endsection
