@extends('admin.layouts.app')

@section('title', 'Carousel dan Banner')
@section('navbar_title', 'Carousel dan Banner')
@section('page_title', 'Carousel dan Banner')
@section('page_description', 'Kelola tampilan carousel dan banner pada aplikasi mobile')

@section('breadcrumb')
    <nav class="flex text-sm mb-6 text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
        <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg></span>
        <span class="text-gray-400">Content Management</span>
        <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg></span>
        <span class="text-gray-900 font-bold">Carousel dan Banner</span>
    </nav>
@endsection

@section('page_actions')
<button type="button" onclick="document.querySelector('[data-open-create-modal]')?.click()" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
    Tambah Slide Baru
</button>
@endsection

@section('content')
<div x-data="carouselManager()" x-init="init()" @open-create-modal.window="showCreateModal = true; createFileName = ''; createMediaPreview = ''; selectedMediaType = 'image';">
    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true; createFileName = ''; createMediaPreview = ''; selectedMediaType = 'image';"></button>
    <!-- Header Summary Panel -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 mb-8 flex flex-wrap items-center justify-between gap-6">
        <div class="flex flex-wrap items-center gap-8">
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status Carousel</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-sans">Menunjukkan status keaktifan tayangan carousel di aplikasi mobile.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-sans">Halaman beranda utama pada aplikasi mobile wisatawan.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                    <span class="font-bold text-gray-800 text-sm">Aktif Meluncur</span>
                </div>
            </div>
            <div class="h-8 w-px bg-gray-100 hidden sm:block"></div>
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Slides</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-sans">Menghitung jumlah total slide/banner aktif yang saat ini sedang ditayangkan.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-sans">Jumlah dots/indikator halaman slider di aplikasi mobile.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <span class="font-bold text-gray-800 text-sm">{{ $banners->where('is_active', true)->count() }} Slide Aktif</span>
            </div>
            <div class="h-8 w-px bg-gray-100 hidden sm:block"></div>
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Auto-Play</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-sans">Mengindikasikan bahwa slide berputar otomatis dengan interval bawaan sistem 3.5 detik per slide.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-sans">Slider beranda utama aplikasi mobile.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-green-600 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    On ({{ $autoplayDuration }}s)
                </div>
            </div>
        </div>
    </div>

        <!-- Main Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column: Slide List & Settings -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Autoplay Settings Card -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6" x-data="{
                    autoplayDuration: {{ $autoplayDuration }},
                    savingSettings: false,
                    async saveSettings() {
                        this.savingSettings = true;
                        try {
                            const response = await fetch('{{ route('admin.carousel_banners.update-settings') }}', {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    carousel_autoplay_duration: this.autoplayDuration
                                })
                            });
                            const result = await window.safeParseJSON(response);
                            if (result.success) {
                                localStorage.setItem('pending_success_toast', result.message || 'Pengaturan berhasil disimpan');
                                window.location.reload();
                            } else {
                                window.showAlert(result.message || 'Gagal menyimpan pengaturan', 'Gagal', 'error');
                            }
                        } catch (error) {
                            console.error(error);
                            window.showAlert('Terjadi kesalahan saat menyimpan pengaturan', 'Error', 'error');
                        } finally {
                            this.savingSettings = false;
                        }
                    }
                }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Durasi Autoplay Gambar
                            </h3>
                            <p class="text-xs text-gray-400">Atur durasi penampilan setiap gambar slide di beranda aplikasi mobile.</p>
                        </div>
                        <div class="flex items-center gap-3 self-end sm:self-center">
                            <div class="relative w-28">
                                <input type="number" x-model="autoplayDuration" min="1" max="60" class="w-full border border-gray-200 rounded-xl pl-4 pr-10 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-bold text-gray-700">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">detik</span>
                            </div>
                            <button @click="saveSettings()" :disabled="savingSettings" class="px-6 py-3 bg-sidebar text-white rounded-xl font-bold hover:opacity-95 transition-all shadow-md shadow-sidebar/10 flex items-center gap-2 text-sm">
                                <svg x-show="savingSettings" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Simpan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Slide List Card -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-1.5">
                            <h3 class="font-bold text-gray-800 text-lg">Urutan Slide Tampilan Utama</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Mengatur prioritas urutan tampil banner di aplikasi mobile dengan fitur drag-and-drop.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-sans">Slider beranda utama aplikasi mobile (slide pertama adalah prioritas tertinggi).</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="text-xs text-gray-400 font-medium flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                            Drag Handle
                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                            </svg>
                            Untuk mengubah urutan
                        </div>
                    </div>

                    <div class="space-y-3" id="slide-list-container">
                        <template x-for="(banner, index) in bannersList" :key="banner.id || banner._id">
                            <div class="flex items-center gap-4 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all hover:border-gray-200 group"
                                :data-id="banner.id || banner._id">
                                <!-- Drag Handle -->
                                <div class="cursor-grab text-gray-200 hover:text-gray-400 pl-1 drag-handle">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                    </svg>
                                </div>

                                <!-- Slide Number -->
                                <div    
                                    class="text-[11px] font-bold text-gray-400 w-16 uppercase tracking-wider slide-number-text" x-text="'SLIDE ' + (index + 1)">
                                </div>

                                <!-- Thumbnail -->
                                <div class="w-28 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100 cursor-pointer hover:scale-105 hover:shadow-md transition-all duration-300 relative"
                                    @click="openMediaPreview(banner.image_url ? (banner.image_url.startsWith('http') ? banner.image_url : '/storage/' + banner.image_url) : null, banner.media_type || 'image')"
                                    title="Klik untuk pratinjau">
                                    <template x-if="banner.image_url && (banner.media_type || 'image') === 'video'">
                                        <div class="w-full h-full bg-gray-900 flex items-center justify-center relative">
                                            <video :src="banner.image_url.startsWith('http') ? banner.image_url : '/storage/' + banner.image_url" class="w-full h-full object-cover opacity-70" muted preload="metadata"></video>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="w-7 h-7 rounded-full bg-white/80 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-gray-800 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            </div>
                                            <span class="absolute bottom-1 left-1 px-1.5 py-0.5 bg-black/60 text-white text-[8px] font-bold rounded">VIDEO</span>
                                        </div>
                                    </template>
                                    <template x-if="banner.image_url && (banner.media_type || 'image') === 'image'">
                                        <img :src="banner.image_url.startsWith('http') ? banner.image_url : '/storage/' + banner.image_url" :alt="banner.title" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!banner.image_url">
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </template>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0 pr-4">
                                    <h4 class="font-bold text-gray-800 text-[15px] truncate" x-text="banner.title"></h4>
                                    <template x-if="banner.subtitle">
                                        <p class="text-[12px] text-gray-500 truncate mt-0.5" x-text="banner.subtitle"></p>
                                    </template>
                                </div>

                                <!-- Badges -->
                                <div class="flex items-center gap-3">
                                    <template x-if="banner.category_badge === 'DESTINASI'">
                                        <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">Destinasi</span>
                                    </template>
                                    <template x-if="banner.category_badge === 'EVENT'">
                                        <span class="px-3 py-1 bg-teal-50 text-teal-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">Event</span>
                                    </template>
                                    <template x-if="banner.category_badge !== 'DESTINASI' && banner.category_badge !== 'EVENT'">
                                        <span class="px-3 py-1 bg-gray-50 text-gray-500 rounded-lg text-[10px] font-bold uppercase tracking-wider" x-text="banner.category_badge"></span>
                                    </template>

                                    <template x-if="banner.is_active">
                                        <span class="px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] text-[10px] font-bold rounded-xl uppercase">Aktif</span>
                                    </template>
                                    <template x-if="!banner.is_active">
                                        <span class="px-4 py-1.5 bg-gray-50 text-gray-400 text-[10px] font-bold rounded-xl uppercase">Nonaktif</span>
                                    </template>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                                    <button @click="openEditModal(banner.id || banner._id)"
                                        class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        @click='$dispatch("open-delete-modal", { action: "/admin/carousel-banners/" + (banner.id || banner._id), title: "Hapus Slide", type: "slide", name: banner.title })'
                                        class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template x-if="bannersList.length === 0">
                            <div class="text-center py-10 text-gray-400 text-sm font-medium">Belum ada slide. Klik "Tambah
                                Slide Baru" untuk membuat.</div>
                        </template>
                    </div>

                    <div class="mt-8 flex gap-4">
                        <button @click="saveOrder()" class="flex-1 py-4 bg-sidebar text-white rounded-2xl font-bold hover:bg-sidebar-hover transition-all shadow-lg shadow-sidebar/20 flex items-center justify-center gap-2 text-sm" :disabled="loadingOrder">
                            <svg x-show="loadingOrder" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg x-show="!loadingOrder" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Simpan Urutan Carousel
                        </button>
                    </div>
                </div>

            </div>

            <!-- Right Column: Mobile Preview -->
            <div class="lg:col-span-1 hidden lg:block">
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 sticky top-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-800 text-sm">Pratinjau Mobile</h3>
                        <span
                            class="text-[9px] font-bold bg-gray-100 text-gray-400 px-2.5 py-1 rounded-md uppercase tracking-wider">Live
                            Preview</span>
                    </div>

                    <!-- Phone Frame -->
                    <div
                        class="relative mx-auto w-[280px] h-[580px] bg-white border-[8px] border-gray-800 rounded-[2.5rem] shadow-xl overflow-hidden shadow-gray-200">
                        <!-- Notch -->
                        <div class="absolute top-0 inset-x-0 h-6 bg-gray-800 rounded-b-xl w-32 mx-auto z-20"></div>

                        <!-- App Content Simulation -->
                        <div class="h-full bg-gray-50 w-full overflow-hidden relative">
                            <!-- Top Navigation -->
                            <div class="flex justify-between items-center px-5 pt-10 pb-4 bg-white">
                                <div class="w-8 h-8 rounded-lg bg-sidebar flex items-center justify-center text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" class="hidden"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 21l7-14 4 8 3-4 4 10H3z"></path>
                                    </svg>
                                </div>
                                <div class="flex gap-3 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>

                        <!-- Banner Preview -->
                        <div class="px-5 mt-2">
                            <template x-if="currentPreview">
                                <div>
                                    <div class="relative w-full h-40 rounded-2xl overflow-hidden shadow-sm cursor-pointer"
                                        @click="openMediaPreview(currentPreview.image_url ? (currentPreview.image_url.startsWith('http') ? currentPreview.image_url : '/storage/' + currentPreview.image_url) : null, currentPreview.media_type || 'image')"
                                        title="Klik untuk pratinjau penuh">

                                        <!-- Video preview (plays according to banner settings, always muted for browser policy) -->
                                        <template x-if="(currentPreview.media_type || 'image') === 'video' && currentPreview.image_url">
                                            <video
                                                :src="currentPreview.image_url.startsWith('http') ? currentPreview.image_url : '/storage/' + currentPreview.image_url"
                                                class="w-full h-full object-cover"
                                                :autoplay="currentPreview.video_autoplay !== false"
                                                :loop="currentPreview.video_loop !== false"
                                                muted
                                                playsinline
                                                preload="auto">
                                            </video>
                                        </template>

                                        <!-- Image preview -->
                                        <template x-if="(currentPreview.media_type || 'image') === 'image' || !currentPreview.image_url">
                                            <img :src="currentPreview.image_url ? (currentPreview.image_url.startsWith('http') ? currentPreview.image_url : '/storage/' + currentPreview.image_url) : 'https://images.unsplash.com/photo-1542332213-9b5a5a3fad35?auto=format&fit=crop&w=400&q=80'" class="w-full h-full object-cover" alt="Banner Preview">
                                        </template>

                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>
                                        <div class="absolute bottom-0 left-0 p-4 w-full pointer-events-none">
                                            <span class="inline-block px-2 py-0.5 bg-sidebar text-white text-[8px] font-bold rounded mb-1 uppercase" x-text="currentPreview.category_badge || 'DESTINASI'"></span>
                                            <h4 class="text-white font-bold text-sm leading-tight" x-text="currentPreview.title"></h4>
                                            <p class="text-white/80 text-[9px] mt-0.5 truncate" x-text="currentPreview.subtitle"></p>
                                        </div>

                                        <!-- Video badge indicator -->
                                        <template x-if="(currentPreview.media_type || 'image') === 'video'">
                                            <span class="absolute top-2 right-2 px-2 py-0.5 bg-black/60 text-white text-[8px] font-bold rounded pointer-events-none">VIDEO</span>
                                        </template>
                                    </div>

                                    <!-- Dots -->
                                    <div class="flex justify-center gap-1.5 mt-3">
                                        <template x-for="(b, i) in activeBanners" :key="i">
                                            <span class="rounded-full transition-all"
                                                :class="i === previewIndex ? 'w-4 h-1.5 bg-sidebar' : 'w-1.5 h-1.5 bg-gray-200'"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!currentPreview">
                                <div class="w-full h-40 bg-gray-200 rounded-2xl flex items-center justify-center text-gray-400 text-xs">No Banners</div>
                            </template>
                        </div>

                            <!-- Categories Dummy -->
                            <div class="px-5 mt-6">
                                <div class="flex justify-between items-end mb-3">
                                    <h4 class="font-bold text-sm text-gray-800">Jelajahi Kategori</h4>
                                    <span class="text-[10px] text-sidebar font-bold">Lihat Semua</span>
                                </div>
                                <div class="flex gap-3 overflow-hidden">
                                    <div
                                        class="w-16 h-16 bg-white rounded-2xl flex flex-col items-center justify-center gap-1 shadow-sm border border-gray-50 flex-shrink-0">
                                        <div
                                            class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4m24-5l-2-2m-2-2l-2-2m-2-2l-2-2">
                                                </path>
                                            </svg>
                                        </div>
                                        <span class="text-[8px] font-bold text-gray-600">Kuliner</span>
                                    </div>
                                    <div
                                        class="w-16 h-16 bg-white rounded-2xl flex flex-col items-center justify-center gap-1 shadow-sm border border-gray-50 flex-shrink-0">
                                        <div
                                            class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-[8px] font-bold text-gray-600">Wisata</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center items-center gap-4 mt-6">
                        <button @click="prevPreview()"
                            class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <span class="text-xs font-bold text-gray-500">Slide <span
                                x-text="activeBanners.length > 0 ? previewIndex + 1 : 0"></span> / <span
                                x-text="activeBanners.length"></span></span>
                        <button @click="nextPreview()"
                            class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Create Modal -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm"
                    @click="showCreateModal = false"></div>

                <div x-show="showCreateModal" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" 
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" 
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative w-full max-w-2xl bg-white shadow-2xl rounded-[2rem] px-8 py-8 text-gray-800 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900">Tambah Slide Carousel</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Tambah Slide Carousel</span>
                                            <p class="text-slate-200 font-normal">Formulir untuk menambahkan slide promosi atau carousel baru ke halaman utama aplikasi mobile wisatawan.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <button @click="showCreateModal = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <template x-if="showCreateModal">
                        <form id="createBannerForm" @submit.prevent="submitCreate()" class="space-y-5">
                            @csrf
                            <!-- Row: Judul Slide & Subjudul -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Judul Slide</label>
                                    <input type="text" name="title" required
                                        placeholder="Contoh: Promo Liburan Akhir Tahun"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Subjudul</label>
                                    <input type="text" name="subtitle"
                                        placeholder="Contoh: Diskon hingga 50% untuk semua destinasi"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                                </div>
                            </div>

                            <!-- Badge Kategori -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Badge Kategori</label>
                                <select name="category_badge" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em] bg-white font-medium text-gray-700" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%23066466%22 stroke-width=%222.5%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')" @change="fetchContents($event.target.value)">
                                    <option value="" disabled selected>Pilih kategori</option>
                                    <option value="DESTINASI">Destinasi</option>
                                    <option value="EVENT">Event</option>
                                    <option value="BERITA_PROMOSI">Berita & Promosi</option>
                                    <option value="BUDAYA">Budaya</option>
                                </select>
                            </div>

                            <!-- Konten Terkait -->
                            <div class="space-y-2" x-show="contentsList.length > 0 || contentLoading" x-transition>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tautkan ke Konten (Opsional)</label>
                                <select name="content_id" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em] bg-white font-medium text-gray-700" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%23066466%22 stroke-width=%222.5%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')" :disabled="contentLoading">
                                    <option value="">Tidak ditautkan...</option>
                                    <template x-for="content in contentsList" :key="content.id">
                                        <option :value="content.id" x-text="content.title"></option>
                                    </template>
                                </select>
                                <span x-show="contentLoading" class="text-xs text-gray-500 animate-pulse">Memuat konten...</span>
                                
                                <input type="hidden" name="content_type" :value="selectedCategory === 'BERITA_PROMOSI' ? 'berita_promosi' : (selectedCategory ? selectedCategory.toLowerCase() : '')">
                            </div>

                            <!-- Tipe Media & File Upload -->
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tipe Media</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="media_type" value="image" x-model="selectedMediaType" class="text-sidebar focus:ring-sidebar" checked>
                                        <span class="text-sm font-semibold text-gray-700">Gambar</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="media_type" value="video" x-model="selectedMediaType" class="text-sidebar focus:ring-sidebar">
                                        <span class="text-sm font-semibold text-gray-700">Video</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Media File Upload -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest" x-text="selectedMediaType === 'video' ? 'File Video (Background)' : 'Gambar Background'"></label>
                                <div class="relative group w-full h-36">
                                    <input type="file" name="image_url" id="image_create" :accept="selectedMediaType === 'video' ? 'video/*' : 'image/*'" required
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                                 const file = $event.target.files[0];
                                                 if (file) {
                                                     const reader = new FileReader();
                                                     reader.onload = (e) => { createMediaPreview = e.target.result; };
                                                     reader.readAsDataURL(file);
                                                 } else {
                                                     createMediaPreview = '';
                                                 }">
                                    <label for="image_create"
                                        class="relative flex flex-col items-center justify-center w-full h-full border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                        <template x-if="createMediaPreview">
                                            <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                <template x-if="selectedMediaType === 'video'">
                                                    <video :src="createMediaPreview" class="w-full h-full object-cover" muted autoplay loop></video>
                                                </template>
                                                <template x-if="selectedMediaType !== 'video'">
                                                    <img :src="createMediaPreview" class="w-full h-full object-cover">
                                                </template>
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <p class="text-white text-xs font-bold" x-text="selectedMediaType === 'video' ? 'Ganti Video' : 'Ganti Gambar'"></p>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!createMediaPreview">
                                            <div class="flex flex-col items-center justify-center text-center px-4">
                                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                    <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Klik atau seret file ke sini'"></p>
                                                <p class="text-[10px] text-gray-400 mt-1" x-text="selectedMediaType === 'video' ? 'MP4, MOV, WEBM (Maks. 50MB)' : 'PNG, JPG, WEBP (Maks. 10MB)'"></p>
                                            </div>
                                        </template>
                                    </label>
                                </div>
                            </div>

                            <!-- Display settings for Video -->
                            <div class="space-y-3 pt-3 border-t border-gray-100 grid grid-cols-2 gap-4" x-show="selectedMediaType === 'video'" x-cloak>
                                <div class="col-span-2 space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Maksimal Durasi Tayang Video (Detik)</label>
                                    <input type="number" name="play_duration" value="10" min="1" max="300" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                                </div>
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="text-xs font-semibold text-gray-700">Autoplay Video</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="video_autoplay" value="on" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="text-xs font-semibold text-gray-700">Loop (Ulang Video)</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="video_loop" value="on" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100 col-span-2">
                                    <span class="text-xs font-semibold text-gray-700">Muted (Suara Dimatikan)</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="video_muted" value="on" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Row: Periode -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Periode Dari</label>
                                    <input type="date" name="start_date"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 outline-none transition-all text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar font-medium">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Periode Sampai</label>
                                    <input type="date" name="end_date"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 outline-none transition-all text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar font-medium">
                                </div>
                            </div>

                            <!-- Status Aktif Box -->
                            <div class="flex items-center justify-between p-4 bg-[#F8F9FA] rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-teal-50 rounded-lg text-teal-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Status Aktif</p>
                                        <p class="text-xs text-gray-400">Tampilkan slide ini di halaman utama</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="on" class="sr-only peer" checked>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar">
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <button type="button" @click="showCreateModal = false"
                                    class="px-8 py-3.5 text-sm font-bold text-gray-400 hover:text-gray-600 border border-gray-200 rounded-xl transition-colors bg-white">Batal</button>
                                <button type="submit"
                                    class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2"
                                    :disabled="loading">
                                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Simpan Slide</span>
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/40 backdrop-blur-sm" 
                     @click="showEditModal = false"></div>

                <div x-show="showEditModal" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" 
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" 
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative w-full max-w-2xl bg-white shadow-2xl rounded-[2rem] px-8 py-8 text-gray-800 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900">Edit Slide Carousel</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Edit Slide Carousel</span>
                                            <p class="text-slate-200 font-normal">Formulir untuk mengubah detail data slide promosi atau carousel yang sudah ada.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <button @click="showEditModal = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div x-show="loading && !editingBanner" class="py-12 flex flex-col items-center justify-center gap-4">
                        <div class="w-12 h-12 border-4 border-sidebar/10 border-t-sidebar rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-sidebar animate-pulse">Memuat data...</p>
                    </div>

                    <template x-if="editingBanner">
                        <form id="editBannerForm" @submit.prevent="submitUpdate()" class="space-y-5">
                            @method('PUT')
                            @csrf

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Judul Slide</label>
                                    <input type="text" name="title" x-model="editingBanner.title" required
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Subjudul</label>
                                    <input type="text" name="subtitle" x-model="editingBanner.subtitle"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Badge Kategori</label>
                                <select name="category_badge" x-model="editingBanner.category_badge" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em] font-medium text-gray-700" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%23066466%22 stroke-width=%222.5%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')" @change="fetchContents($event.target.value)">
                                    <option value="DESTINASI">Destinasi</option>
                                    <option value="EVENT">Event</option>
                                    <option value="BERITA_PROMOSI">Berita & Promosi</option>
                                    <option value="BUDAYA">Budaya</option>
                                </select>
                            </div>

                            <!-- Konten Terkait -->
                            <div class="space-y-2" x-show="contentsList.length > 0 || contentLoading" x-transition>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tautkan ke Konten (Opsional)</label>
                                <select name="content_id" x-model="editingBanner.content_id" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em] bg-white font-medium text-gray-700" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%23066466%22 stroke-width=%222.5%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')" :disabled="contentLoading">
                                    <option value="">Tidak ditautkan...</option>
                                    <template x-for="content in contentsList" :key="content.id">
                                        <option :value="content.id" x-text="content.title"></option>
                                    </template>
                                </select>
                                <span x-show="contentLoading" class="text-xs text-gray-500 animate-pulse">Memuat konten...</span>
                                
                                <input type="hidden" name="content_type" :value="selectedCategory === 'BERITA_PROMOSI' ? 'berita_promosi' : (selectedCategory ? selectedCategory.toLowerCase() : '')">
                            </div>

                            <!-- Tipe Media Selection -->
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tipe Media</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="media_type" value="image" x-model="selectedMediaType" class="text-sidebar focus:ring-sidebar">
                                        <span class="text-sm font-semibold text-gray-700">Gambar</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="media_type" value="video" x-model="selectedMediaType" class="text-sidebar focus:ring-sidebar">
                                        <span class="text-sm font-semibold text-gray-700">Video</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Current Media Preview -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest" x-text="selectedMediaType === 'video' ? 'Video Saat Ini' : 'Gambar Saat Ini'"></label>
                                <template x-if="editingBanner?.image_url">
                                    <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 h-32 w-full border border-gray-100 mb-3 group cursor-pointer" @click="openMediaPreview(editingBanner.image_url.startsWith('http') ? editingBanner.image_url : '/storage/' + editingBanner.image_url, selectedMediaType)" title="Klik untuk pratinjau">
                                        <template x-if="selectedMediaType === 'video'">
                                            <div class="w-full h-full relative">
                                                <video :src="editingBanner.image_url.startsWith('http') ? editingBanner.image_url : '/storage/' + editingBanner.image_url" class="w-full h-full object-cover" muted></video>
                                                <div class="absolute inset-0 bg-black/25 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-white opacity-70 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="selectedMediaType !== 'video'">
                                            <img :src="editingBanner.image_url.startsWith('http') ? editingBanner.image_url : '/storage/' + editingBanner.image_url" class="w-full h-full object-cover" alt="Media Saat Ini">
                                        </template>
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-xl" x-text="selectedMediaType === 'video' ? 'Pratinjau Video (Klik untuk memutar)' : 'Gambar Saat Ini (Klik untuk memperbesar)'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Upload File Input -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest" x-text="selectedMediaType === 'video' ? 'Ganti File Video' : 'Ganti Gambar Background'"></label>
                                <div class="relative group w-full h-36">
                                    <input type="file" name="image_url" id="image_edit" :accept="selectedMediaType === 'video' ? 'video/*' : 'image/*'" class="hidden"
                                         @change="fileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                                 const file = $event.target.files[0];
                                                 if (file) {
                                                     const reader = new FileReader();
                                                     reader.onload = (e) => { editMediaPreview = e.target.result; };
                                                     reader.readAsDataURL(file);
                                                 } else {
                                                     editMediaPreview = '';
                                                 }">
                                    <label for="image_edit"
                                        class="relative flex flex-col items-center justify-center w-full h-full border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                        <template x-if="editMediaPreview">
                                            <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                <template x-if="selectedMediaType === 'video'">
                                                    <video :src="editMediaPreview" class="w-full h-full object-cover" muted autoplay loop></video>
                                                </template>
                                                <template x-if="selectedMediaType !== 'video'">
                                                    <img :src="editMediaPreview" class="w-full h-full object-cover">
                                                </template>
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <p class="text-white text-xs font-bold" x-text="selectedMediaType === 'video' ? 'Ganti Video' : 'Ganti Gambar'"></p>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!editMediaPreview">
                                            <div class="flex flex-col items-center justify-center text-center px-4">
                                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                    <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-bold text-gray-700" x-text="fileName || 'Klik atau seret file baru ke sini'"></p>
                                                <p class="text-[10px] text-gray-400 mt-1" x-text="selectedMediaType === 'video' ? 'MP4, MOV, WEBM (Maks. 50MB) - Biarkan kosong jika tidak ingin diubah' : 'PNG, JPG, WEBP (Maks. 10MB) - Biarkan kosong jika tidak ingin diubah'"></p>
                                            </div>
                                        </template>
                                    </label>
                                </div>
                            </div>

                            <!-- Display settings for Video -->
                            <div class="space-y-3 pt-3 border-t border-gray-100 grid grid-cols-2 gap-4" x-show="selectedMediaType === 'video'" x-cloak>
                                <div class="col-span-2 space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Maksimal Durasi Tayang Video (Detik)</label>
                                    <input type="number" name="play_duration" x-model="editingBanner.play_duration" min="1" max="300" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 outline-none text-sm focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar font-medium text-gray-700">
                                </div>
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="text-xs font-semibold text-gray-700">Autoplay Video</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="video_autoplay" value="on" class="sr-only peer" x-model="editingBanner.video_autoplay">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="text-xs font-semibold text-gray-700">Loop (Ulang Video)</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="video_loop" value="on" class="sr-only peer" x-model="editingBanner.video_loop">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100 col-span-2">
                                    <span class="text-xs font-semibold text-gray-700">Muted (Suara Dimatikan)</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="video_muted" value="on" class="sr-only peer" x-model="editingBanner.video_muted">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Periode Dari</label>
                                    <input type="date" name="start_date" x-model="editingBanner.start_date_formatted"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 outline-none transition-all text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar font-medium">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Periode Sampai</label>
                                    <input type="date" name="end_date" x-model="editingBanner.end_date_formatted"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-3.5 outline-none transition-all text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar font-medium">
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-[#F8F9FA] rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-teal-50 rounded-lg text-teal-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Status Aktif</p>
                                        <p class="text-xs text-gray-400">Tampilkan slide ini di halaman utama</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="on" class="sr-only peer"
                                        x-model="editingBanner.is_active">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar">
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <button type="button" @click="showEditModal = false"
                                    class="px-8 py-3.5 text-sm font-bold text-gray-400 hover:text-gray-600 border border-gray-200 rounded-xl transition-colors bg-white">Batal</button>
                                <button type="submit"
                                    class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2"
                                    :disabled="loading">
                                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    <!-- Media Lightbox Modal (supports both image & video) -->
    <div x-show="showLightbox" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm" x-cloak
        @click="showLightbox = false; lightboxMediaType = 'image';"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative max-w-4xl max-h-[90vh] p-4 flex items-center justify-center" @click.stop>

            <!-- Image lightbox -->
            <template x-if="lightboxMediaType === 'image'">
                <img :src="lightboxImage" class="max-w-[95vw] max-h-[85vh] rounded-3xl object-contain shadow-2xl border border-white/10"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            </template>

            <!-- Video lightbox -->
            <template x-if="lightboxMediaType === 'video'">
                <video :src="lightboxImage" class="max-w-[95vw] max-h-[85vh] rounded-3xl shadow-2xl border border-white/10"
                    controls autoplay
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                </video>
            </template>

            <button @click="showLightbox = false; lightboxMediaType = 'image';" class="absolute -top-12 right-0 p-3 bg-black/60 text-white rounded-full hover:bg-black/80 transition-colors border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function carouselManager() {
    return {
        bannersList: @json($banners),
        previewIndex: 0,
        loadingOrder: false,
        
        get activeBanners() {
            return this.bannersList.filter(b => b.is_active);
        },
        
        get currentPreview() {
            return this.activeBanners.length > 0 ? this.activeBanners[this.previewIndex] : null;
        },
        
        nextPreview() {
            if (this.activeBanners.length === 0) return;
            this.previewIndex = (this.previewIndex + 1) % this.activeBanners.length;
        },
        
        prevPreview() {
            if (this.activeBanners.length === 0) return;
            this.previewIndex = (this.previewIndex - 1 + this.activeBanners.length) % this.activeBanners.length;
        },

                initSortable() {
                    const list = document.getElementById('slide-list-container');
                    if (list && typeof Sortable !== 'undefined') {
                        Sortable.create(list, {
                            handle: '.drag-handle',
                            animation: 150,
                            onEnd: (evt) => {
                                const oldIdx = evt.oldDraggableIndex;
                                const newIdx = evt.newDraggableIndex;

                                if (oldIdx !== undefined && newIdx !== undefined && oldIdx !== newIdx) {
                                    // 1. Batalkan perubahan DOM fisik dari Sortable
                                    const item = evt.item;
                                    const parent = item.parentNode;
                                    parent.removeChild(item);
                                    const referenceNode = parent.children[evt.oldIndex];
                                    if (referenceNode) {
                                        parent.insertBefore(item, referenceNode);
                                    } else {
                                        parent.appendChild(item);
                                    }

                                    // 2. Paksa Alpine merender ulang array
                                    const newList = [...this.bannersList];
                                    const movedItem = newList.splice(oldIdx, 1)[0];
                                    newList.splice(newIdx, 0, movedItem);
                                    
                                    this.bannersList = newList;
                                    this.previewIndex = 0; // Reset preview
                                }
                            }
                        });
                    }
                },

        async saveOrder() {
            this.loadingOrder = true;
            const orders = this.bannersList.map((banner, index) => ({
                id: banner.id || banner._id,
                order: index + 1
            }));

            try {
                const response = await fetch('/admin/carousel-banners/order', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ orders })
                });
                
                const result = await window.safeParseJSON(response);
                if (result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Urutan carousel berhasil disimpan');
                    window.location.reload();
                } else {
                    window.showAlert('Gagal menyimpan urutan: ' + result.message, 'Gagal', 'error');
                }
            } catch (e) {
                console.error('Failed to sort', e);
                window.showAlert('Terjadi kesalahan saat menyimpan urutan.', 'Error', 'error');
            } finally {
                this.loadingOrder = false;
            }
        },

        init() {
            this.initSortable();
            // Start smart autoplay for live preview:
            // - Image slides use global carousel_autoplay_duration
            // - Video slides use their own play_duration setting
            if (this.activeBanners.length > 1) {
                this.scheduleNextSlide();
            }
        },

        _autoplayTimer: null,

        scheduleNextSlide() {
            if (this._autoplayTimer) clearTimeout(this._autoplayTimer);
            if (this.activeBanners.length <= 1) return;

            const current = this.currentPreview;
            let delay;
            if (current && current.media_type === 'video' && current.play_duration) {
                // Video slide: use play_duration (max seconds video is shown before advancing)
                delay = current.play_duration * 1000;
            } else {
                // Image slide: use global autoplay duration
                delay = {{ $autoplayDuration * 1000 }};
            }

            this._autoplayTimer = setTimeout(() => {
                this.nextPreview();
                this.scheduleNextSlide();
            }, delay);
        },

        showEditModal: false,
        showCreateModal: false,
        editingBanner: null,
        loading: false,
        fileName: '',
        createFileName: '',
        createMediaPreview: '',
        editMediaPreview: '',
        selectedMediaType: 'image',
        showLightbox: false,
        lightboxImage: '',
        lightboxMediaType: 'image',
        contentsList: [],
        selectedCategory: '',
        contentLoading: false,

        openMediaPreview(url, mediaType = 'image') {
            if (!url) return;
            this.lightboxImage = url;
            this.lightboxMediaType = mediaType;
            this.showLightbox = true;
        },

        async fetchContents(category) {
            if (!category) {
                this.contentsList = [];
                this.selectedCategory = category;
                return;
            }
            this.selectedCategory = category;
            this.contentLoading = true;
            try {
                const response = await fetch(`/admin/carousel-banners/contents-by-category?category=${category}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await window.safeParseJSON(response);
                if (result.success) {
                    this.contentsList = result.data || [];
                }
            } catch (e) {
                console.error('Failed to fetch contents', e);
            } finally {
                this.contentLoading = false;
            }
        },
        
        async openEditModal(id) {
            this.loading = true;
            this.showEditModal = true;
            this.editingBanner = null;
            this.fileName = '';
            this.editMediaPreview = '';
            try {
                const response = await fetch(`/admin/carousel-banners/${id}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.editingBanner = await window.safeParseJSON(response);
                // Ensure _id is present for submission
                if (this.editingBanner && !this.editingBanner._id && this.editingBanner.id) {
                    this.editingBanner._id = this.editingBanner.id;
                }
                this.fileName = this.editingBanner.image_url ? 'Gambar saat ini' : '';
                this.selectedMediaType = this.editingBanner.media_type || 'image';
                
                if (this.editingBanner.category_badge) {
                    await this.fetchContents(this.editingBanner.category_badge);
                }
            } catch (error) {
                window.showAlert('Gagal mengambil data banner', 'Gagal', 'error');
                this.showEditModal = false;
            } finally {
                this.loading = false;
            }
        },
        
        async submitUpdate() {
            const bannerId = this.editingBanner._id || this.editingBanner.id;
            if (!bannerId) {
                window.showAlert('ID Banner tidak ditemukan', 'Perhatian', 'warning');
                return;
            }

            this.loading = true;
            const form = document.getElementById('editBannerForm');
            const formData = new FormData(form);
            
            try {
                const response = await fetch(`/admin/carousel-banners/${bannerId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const result = await window.safeParseJSON(response);

                if (result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Slide carousel berhasil diperbarui');
                    window.location.reload();
                } else {
                    window.showAlert(result.message || 'Gagal memperbarui banner', 'Gagal', 'error');
                }
            } catch (error) {
                console.error(error);
                window.showAlert(error.message || 'Terjadi kesalahan saat menyimpan data', 'Error', 'error');
            } finally {
                this.loading = false;
            }
        },

        async submitCreate() {
            this.loading = true;
            const form = document.getElementById('createBannerForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('/admin/carousel-banners', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const result = await window.safeParseJSON(response);

                if (result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Slide carousel berhasil ditambahkan');
                    window.location.reload();
                } else {
                    window.showAlert(result.message || 'Gagal membuat banner', 'Gagal', 'error');
                }
            } catch (error) {
                console.error(error);
                window.showAlert(error.message || 'Terjadi kesalahan saat menyimpan data', 'Error', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
