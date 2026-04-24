@extends('admin.layouts.app')

@section('title', 'Berita & Promosi')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Home</a>
                <span class="mx-2">›</span>
                <span>Content Management</span>
                <span class="mx-2">›</span>
                <span class="text-gray-900 font-medium">Berita & Promosi</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Berita & Promosi</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola publikasi informasi, artikel, dan promo menarik di Danau Toba.</p>
        </div>
        <div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-add-modal'))" class="bg-sidebar hover:bg-sidebar-hover text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Berita/Promosi
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-[1.5rem] p-4 mb-6 shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center">
    <form action="{{ route('admin.berita_promosi.index') }}" method="GET" class="flex flex-wrap w-full gap-4 items-center" id="filter-form">
        <div class="relative flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita atau promosi.." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <select name="tipe" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary" onchange="document.getElementById('filter-form').submit()">
            <option value="">Semua Tipe</option>
            <option value="BERITA" {{ request('tipe') == 'BERITA' ? 'selected' : '' }}>Berita</option>
            <option value="PROMO" {{ request('tipe') == 'PROMO' ? 'selected' : '' }}>Promo</option>
            <option value="EVENT" {{ request('tipe') == 'EVENT' ? 'selected' : '' }}>Event</option>
        </select>
        <select name="status" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary" onchange="document.getElementById('filter-form').submit()">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
        </select>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Dari:</span>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary" onchange="document.getElementById('filter-form').submit()">
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Sampai:</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary" onchange="document.getElementById('filter-form').submit()">
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">JUDUL</th>
                    <th class="px-6 py-4">TIPE</th>
                    <th class="px-6 py-4">TANGGAL TAYANG</th>
                    <th class="px-6 py-4">STATUS</th>
                    <th class="px-6 py-4 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($beritaPromosi as $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($item->thumbnail)
                                <div class="w-12 h-8 rounded-lg overflow-hidden mr-3 border border-gray-200 flex-shrink-0 shadow-sm">
                                    <img src="{{ Storage::url($item->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-12 h-8 rounded-lg bg-gray-100 mr-3 border border-dashed border-gray-300 flex-shrink-0 flex items-center justify-center text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="font-bold text-gray-900 text-sm">{{ $item->judul }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($item->tipe === 'EVENT')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">EVENT</span>
                        @elseif($item->tipe === 'PROMO')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-500">PROMO</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-600">BERITA</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $item->tanggal_tayang ? $item->tanggal_tayang->format('d F Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($item->is_active)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">Aktif</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button type="button" onclick="editItem('{{ $item->_id }}')" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </button>
                        <button type="button" 
                            @click="$dispatch('open-delete-modal', { 
                                action: '{{ route('admin.berita_promosi.destroy', (string)$item->_id) }}', 
                                title: 'Hapus Konten', 
                                type: '{{ strtolower($item->tipe) }}', 
                                name: '{{ addslashes($item->judul) }}' 
                            })" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada entri data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
        <div>Menampilkan {{ $beritaPromosi->count() }} dari {{ $beritaPromosi->total() }} entri</div>
        <div>
            {{ $beritaPromosi->links() }}
        </div>
    </div>
</div>

<!-- Tambah Modal -->
<div x-data="{ show: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }" @open-add-modal.window="show = true" x-show="show" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-2xl text-left align-middle transition-all transform bg-white shadow-2xl rounded-[1.5rem] sm:my-8 relative z-10">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900">Tambah Berita/Promosi</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('admin.berita_promosi.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Masukkan judul berita atau promosi" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('judul') border-red-500 @enderror">
                        @error('judul') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select name="tipe" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm appearance-none bg-white @error('tipe') border-red-500 @enderror">
                            <option value="BERITA" {{ old('tipe') == 'BERITA' ? 'selected' : '' }}>BERITA</option>
                            <option value="PROMO" {{ old('tipe') == 'PROMO' ? 'selected' : '' }}>PROMO</option>
                            <option value="EVENT" {{ old('tipe') == 'EVENT' ? 'selected' : '' }}>EVENT</option>
                        </select>
                        @error('tipe') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div x-data="{ fileName: '', previewUrl: '' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors relative overflow-hidden">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="absolute inset-0 w-full h-full object-cover opacity-20 pointer-events-none">
                            </template>
                            <input type="file" name="thumbnail" required accept="image/png, image/jpeg, image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                @change="
                                    fileName = $event.target.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { previewUrl = e.target.result };
                                    reader.readAsDataURL($event.target.files[0]);
                                ">
                            <svg class="w-8 h-8 text-sidebar mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <p class="text-sm text-gray-700 font-medium" x-text="fileName || 'Klik untuk upload foto'"></p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG (Maks. 2MB, Rekomendasi 1920x1080px)</p>
                        </div>
                        @error('thumbnail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                        <textarea name="konten" required rows="4" placeholder="Tulis konten berita/promosi di sini..." class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('konten') border-red-500 @enderror">{{ old('konten') }}</textarea>
                        @error('konten') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tayang</label>
                        <input type="date" name="tanggal_tayang" value="{{ old('tanggal_tayang') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('tanggal_tayang') border-red-500 @enderror">
                        @error('tanggal_tayang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="text-sm font-medium text-gray-700">Tampilkan di Carousel</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="tampilkan_di_carousel" {{ old('tampilkan_di_carousel') ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="text-sm font-medium text-gray-700">Status Aktif <span class="text-xs text-gray-500 font-normal ml-1">(Draft jika nonaktif)</span></span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                        </label>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="show = false" class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebar-hover text-white rounded-xl transition-colors text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div x-data="{ show: false }" @open-edit-modal.window="show = true" x-show="show" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-2xl text-left align-middle transition-all transform bg-white shadow-2xl rounded-[1.5rem] sm:my-8 relative z-10">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900">Edit Berita/Promosi</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                        <input type="text" id="edit_judul" name="judul" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('judul') border-red-500 @enderror">
                        @error('judul') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select id="edit_tipe" name="tipe" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm appearance-none bg-white @error('tipe') border-red-500 @enderror">
                            <option value="BERITA">BERITA</option>
                            <option value="PROMO">PROMO</option>
                            <option value="EVENT">EVENT</option>
                        </select>
                        @error('tipe') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div x-data="{ fileName: '', previewUrl: '' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors relative overflow-hidden">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="absolute inset-0 w-full h-full object-cover opacity-20 pointer-events-none">
                            </template>
                            <input type="file" name="thumbnail" accept="image/png, image/jpeg, image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                @change="
                                    fileName = $event.target.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { previewUrl = e.target.result };
                                    reader.readAsDataURL($event.target.files[0]);
                                ">
                            <svg class="w-8 h-8 text-sidebar mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <p class="text-sm text-gray-700 font-medium" x-text="fileName || 'Klik untuk ganti foto'"></p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG (Maks. 2MB, Rekomendasi 1920x1080px)</p>
                            <p class="text-xs text-gray-400 mt-1 italic">Biarkan kosong jika tidak ingin mengubah gambar</p>
                        </div>
                        @error('thumbnail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                        <textarea id="edit_konten" name="konten" required rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('konten') border-red-500 @enderror"></textarea>
                        @error('konten') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tayang</label>
                        <input type="date" id="edit_tanggal" name="tanggal_tayang" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('tanggal_tayang') border-red-500 @enderror">
                        @error('tanggal_tayang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="text-sm font-medium text-gray-700">Tampilkan di Carousel <span class="text-xs text-gray-500 font-normal ml-1">Konten akan muncul di banner utama</span></span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="edit_carousel" name="tampilkan_di_carousel" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                        </label>
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
                    <button type="button" @click="show = false" class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebar-hover text-white rounded-xl transition-colors text-sm font-medium">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Custom Success Alert Modal -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-sm p-10 text-center align-middle transition-all transform bg-white shadow-2xl rounded-[1.5rem] sm:my-8 text-gray-800 relative z-10" x-init="setTimeout(() => show = false, 2500)">
            <div class="w-20 h-20 bg-[#cbf4f5] rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">{{ session('success') }}</h3>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function editItem(id) {
        fetch(`/admin/berita-promosi/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('editForm').action = `/admin/berita-promosi/${id}`;
                document.getElementById('edit_judul').value = data.judul;
                document.getElementById('edit_tipe').value = data.tipe;
                document.getElementById('edit_konten').value = data.konten;
                document.getElementById('edit_tanggal').value = data.tanggal_tayang_formatted || '';
                document.getElementById('edit_carousel').checked = data.tampilkan_di_carousel;
                document.getElementById('edit_status').checked = data.is_active;
                
                window.dispatchEvent(new CustomEvent('open-edit-modal'));
            })
            .catch(err => {
                alert('Gagal mengambil data: ' + err);
            });
    }
</script>
@endpush
