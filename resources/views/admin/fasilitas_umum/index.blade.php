@extends('admin.layouts.app')

@section('title', 'Kelola Fasilitas Umum')
@section('page_title', 'Fasilitas Umum')
@section('page_description', 'Kelola data fasilitas umum di kawasan Danau Toba')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
    <a href="#" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </span>
    <span class="text-gray-900 font-bold">Fasilitas Umum</span>
</nav>
@endsection

@section('content')
<div x-data="facilityManager()">
    <!-- Header Summary Panel & Add Button -->
    <div class="flex flex-wrap items-center justify-between gap-6 mb-8">
        <!-- Filter Tabs (Matching Image) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 flex flex-wrap gap-1">
            <template x-for="type in ['Semua', 'SPBU', 'Hotel', 'Resto', 'RS/Puskesmas', 'ATM']">
                <button 
                    @click="filterByType(type)"
                    :class="activeType === type ? 'bg-sidebar text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'"
                    class="px-6 py-2 rounded-xl text-sm font-bold transition-all"
                    x-text="type">
                </button>
            </template>
        </div>
        
        <button @click="showCreateModal = true" class="flex items-center gap-2 px-6 py-3 bg-sidebar text-white rounded-xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Fasilitas
        </button>
    </div>

    <!-- Filters & Search Bar (Matching Image) -->
    <div class="flex flex-wrap items-center gap-4 mb-8">
        <div class="relative flex-1 min-w-[300px]">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchData()" placeholder="Cari nama, alamat, jenis..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm transition-all shadow-sm placeholder-gray-300">
        </div>

        <select x-model="statusFilter" @change="fetchData()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 outline-none text-sm shadow-sm transition-all text-gray-600 font-medium">
            <option value="all">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
        </select>

        <div class="flex items-center gap-2">
            <input type="date" class="px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sidebar/10 outline-none text-sm shadow-sm">
            <span class="text-gray-300">-</span>
            <input type="date" class="px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-sidebar/10 outline-none text-sm shadow-sm">
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    <tr>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-400 uppercase tracking-wider">Fasilitas</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-400 uppercase tracking-wider">Alamat</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-400 uppercase tracking-wider">Jam Buka</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($facilities as $facility)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    @if($facility->image_url)
                                        <img src="{{ asset('storage/' . $facility->image_url) }}" alt="{{ $facility->name }}" class="w-20 h-14 object-cover rounded-xl shadow-sm border border-gray-100 flex-shrink-0">
                                    @else
                                        <div class="w-20 h-14 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="text-[14px] font-bold text-gray-800">{{ $facility->name }}</div>
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
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[11px] font-bold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-500 rounded-lg text-[11px] font-bold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal('{{ $facility->_id }}')" class="flex items-center gap-1.5 px-4 py-2 bg-teal-50 text-teal-600 rounded-xl text-xs font-bold hover:bg-teal-100 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </button>
                                    <button @click="$dispatch('open-delete-modal', { action: '{{ route('admin.fasilitas_umum.destroy', $facility->_id) }}', title: 'Hapus Fasilitas', type: 'fasilitas', name: '{{ $facility->name }}' })" class="flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-400 rounded-xl text-xs font-bold hover:bg-red-100 hover:text-red-500 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-10 py-20 text-center">
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
                {{ $facilities->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @endif
    </div>

    <!-- Create Modal -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/20 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-xl px-8 py-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2.5rem] sm:my-8 text-gray-800">
                
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold">Tambah Fasilitas Umum</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="submitCreate()" class="space-y-6">
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
                            <input type="text" name="latitude" placeholder="Contoh: 2.3361" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Longitude</label>
                            <input type="text" name="longitude" placeholder="Contoh: 99.0494" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                        </div>
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

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Foto Fasilitas</label>
                        <div class="relative group">
                            <input type="file" name="image" id="create_image" class="hidden" @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            <label for="create_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Upload foto (Opsional)'"></p>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tight">PNG, JPG, WEBP (Maks. 5MB)</p>
                            </label>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-50 rounded-2xl flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">Status Aktif</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Aktifkan untuk menampilkan di aplikasi</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" checked class="sr-only peer">
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
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" class="fixed inset-0 transition-opacity bg-black/20 backdrop-blur-sm" @click="showEditModal = false"></div>

            <div x-show="showEditModal" class="inline-block w-full max-w-xl px-8 py-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2.5rem] sm:my-8 text-gray-800">
                
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold">Edit Fasilitas Umum</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !editingFacility" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <form x-show="editingFacility" @submit.prevent="submitUpdate()" class="space-y-6">
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
                            <input type="text" name="latitude" x-model="editingFacility.latitude" placeholder="Contoh: 2.3361" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Longitude</label>
                            <input type="text" name="longitude" x-model="editingFacility.longitude" placeholder="Contoh: 99.0494" class="w-full border border-gray-100 bg-gray-50/50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-bold placeholder-gray-300">
                        </div>
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

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Ganti Foto Fasilitas</label>
                        <div x-show="editingFacility && editingFacility.image_url" class="mb-3">
                            <img :src="`/storage/${editingFacility.image_url}`" class="w-full h-40 object-cover rounded-2xl shadow-sm border border-gray-100">
                            <p class="text-[10px] text-gray-400 mt-1">Foto saat ini</p>
                        </div>
                        <div class="relative group">
                            <input type="file" name="image" id="edit_image" class="hidden" @change="editFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            <label for="edit_image" class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                                <p class="text-[13px] font-bold text-gray-700" x-text="editFileName || 'Pilih foto baru'"></p>
                            </label>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-50 rounded-2xl flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">Status Aktif</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Aktifkan untuk menampilkan di aplikasi</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" :checked="editingFacility.is_active" class="sr-only peer">
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

<script>
function facilityManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        loading: false,
        activeType: '{{ request('type', 'Semua') }}',
        searchQuery: '{{ request('search', '') }}',
        statusFilter: '{{ request('status', 'all') }}',
        editingFacility: null,
        createFileName: '',
        editFileName: '',

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

        async openEditModal(id) {
            this.loading = true;
            this.showEditModal = true;
            this.editingFacility = null;
            this.editFileName = '';
            try {
                const response = await fetch(`/admin/fasilitas-umum/${id}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.editingFacility = await response.json();
            } catch (error) {
                alert('Gagal mengambil data fasilitas');
                this.showEditModal = false;
            } finally {
                this.loading = false;
            }
        },

        async submitCreate() {
            this.loading = true;
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('{{ route('admin.fasilitas_umum.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal menambahkan fasilitas');
                }
            } catch (error) {
                alert('Terjadi kesalahan saat menyimpan data');
            } finally {
                this.loading = false;
            }
        },

        async submitUpdate() {
            this.loading = true;
            const form = event.target;
            const formData = new FormData(form);
            formData.append('_method', 'PUT');
            
            try {
                const response = await fetch(`/admin/fasilitas-umum/${this.editingFacility._id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal memperbarui fasilitas');
                }
            } catch (error) {
                alert('Terjadi kesalahan saat menyimpan data');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
