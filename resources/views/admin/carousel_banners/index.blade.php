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
<div x-data="carouselManager()" x-init="init()" @open-create-modal.window="showCreateModal = true">
    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true"></button>
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
                    On (3.5s)
                </div>
            </div>
        </div>
    </div>

        <!-- Main Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column: Slide List & Settings -->
            <div class="lg:col-span-2 space-y-8">

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
                                <div class="w-28 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100 cursor-pointer hover:scale-105 hover:shadow-md transition-all duration-300" @click="lightboxImage = (banner.image_url.startsWith('http') ? banner.image_url : '/storage/' + banner.image_url); showLightbox = true" title="Klik untuk memperbesar">
                                    <template x-if="banner.image_url">
                                        <img :src="banner.image_url.startsWith('http') ? banner.image_url : '/storage/' + banner.image_url" :alt="banner.title" class="w-full h-full object-cover">
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
                                    <div class="relative w-full h-40 rounded-2xl overflow-hidden shadow-sm cursor-pointer" @click="lightboxImage = (currentPreview.image_url ? (currentPreview.image_url.startsWith('http') ? currentPreview.image_url : '/storage/' + currentPreview.image_url) : 'https://images.unsplash.com/photo-1542332213-9b5a5a3fad35?auto=format&fit=crop&w=400&q=80'); showLightbox = true" title="Klik untuk memperbesar">
                                        <img :src="currentPreview.image_url ? (currentPreview.image_url.startsWith('http') ? currentPreview.image_url : '/storage/' + currentPreview.image_url) : 'https://images.unsplash.com/photo-1542332213-9b5a5a3fad35?auto=format&fit=crop&w=400&q=80'" class="w-full h-full object-cover" alt="Banner Preview">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 p-4 w-full">
                                            <span class="inline-block px-2 py-0.5 bg-sidebar text-white text-[8px] font-bold rounded mb-1 uppercase" x-text="currentPreview.category_badge || 'DESTINASI'"></span>
                                            <h4 class="text-white font-bold text-sm leading-tight" x-text="currentPreview.title"></h4>
                                            <p class="text-white/80 text-[9px] mt-0.5 truncate" x-text="currentPreview.subtitle"></p>
                                        </div>
                                    </div>
                                    <!-- Dots -->
                                    <div class="flex justify-center gap-1.5 mt-3">
                                        <template x-for="(b, i) in activeBanners" :key="i">
                                            <span class="rounded-full transition-all" 
                                                :class="i === previewIndex ? 'w-4 h-1.5 bg-[#6349A5]' : 'w-1.5 h-1.5 bg-gray-200'"></span>
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


        <!-- Create Modal (From Image 2) -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm"
                    @click="showCreateModal = false"></div>

                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    class="relative w-full max-w-2xl bg-white shadow-2xl rounded-[2rem] px-8 py-8 text-gray-800 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold">Tambah Slide Carousel</h3>
                        <button @click="showCreateModal = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="createBannerForm" @submit.prevent="submitCreate()" class="space-y-5">
                        @csrf
                        <!-- Row: Judul Slide & Subjudul -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Judul Slide</label>
                                <input type="text" name="title" required
                                    placeholder="Contoh: Promo Liburan Akhir Tahun"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Subjudul</label>
                                <input type="text" name="subtitle"
                                    placeholder="Contoh: Diskon hingga 50% untuk semua destinasi"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm">
                            </div>
                        </div>

                    <!-- Badge Kategori -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Badge Kategori</label>
                        <select name="category_badge" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%239CA3AF%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')" @change="fetchContents($event.target.value)">
                            <option value="" disabled selected>Pilih kategori</option>
                            <option value="DESTINASI">Destinasi</option>
                            <option value="EVENT">Event</option>
                            <option value="BERITA_PROMOSI">Berita & Promosi</option>
                            <option value="BUDAYA">Budaya</option>
                        </select>
                    </div>

                    <!-- Konten Terkait -->
                    <div class="space-y-1.5" x-show="contentsList.length > 0 || contentLoading" x-transition>
                        <label class="block text-sm font-medium text-gray-700">Tautkan ke Konten (Opsional)</label>
                        <select name="content_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm" :disabled="contentLoading">
                            <option value="">Tidak ditautkan...</option>
                            <template x-for="content in contentsList" :key="content.id">
                                <option :value="content.id" x-text="content.title"></option>
                            </template>
                        </select>
                        <span x-show="contentLoading" class="text-xs text-gray-500 animate-pulse">Memuat konten...</span>
                        
                        <input type="hidden" name="content_type" :value="selectedCategory === 'BERITA_PROMOSI' ? 'berita_promosi' : (selectedCategory ? selectedCategory.toLowerCase() : '')">
                    </div>

                        <!-- Gambar Background -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">Gambar Background</label>
                            <div class="relative group">
                                <input type="file" name="image_url" id="image_create" accept="image/*" required
                                    class="hidden"
                                    @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                <label for="image_create"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#6349A5]/30 rounded-xl cursor-pointer hover:bg-gray-50 transition-all bg-[#F8F7FA]">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-6 h-6 text-[#6349A5] mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        <p class="text-sm font-bold text-[#6349A5]"
                                            x-text="createFileName || 'Klik atau seret file ke sini'"></p>
                                        <p class="text-xs text-gray-400 mt-1">PNG, JPG (Maks. 2MB, Rekomendasi 1920x1080px)
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Row: Periode -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Periode Dari</label>
                                <input type="date" name="start_date"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none transition-all text-sm text-gray-600 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5]">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Periode Sampai</label>
                                <input type="date" name="end_date"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none transition-all text-sm text-gray-600 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5]">
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
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#6349A5]">
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="showCreateModal = false"
                                class="px-6 py-2.5 text-sm font-extrabold text-gray-600 hover:text-gray-800 transition-colors border border-gray-200 rounded-lg bg-white">Batal</button>
                            <button type="submit"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-[#6349A5] hover:bg-[#523A91] rounded-lg shadow-md transition-all flex items-center gap-2"
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
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div x-show="showEditModal" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm"
                    @click="showEditModal = false"></div>

                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="relative w-full max-w-2xl bg-white shadow-2xl rounded-[2rem] px-8 py-8 text-gray-800 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                        <h3 class="text-xl font-bold">Edit Slide Carousel</h3>
                        <button @click="showEditModal = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div x-show="loading && !editingBanner" class="py-12 flex justify-center">
                        <svg class="animate-spin h-8 w-8 text-[#6349A5]" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <form id="editBannerForm" x-show="editingBanner" @submit.prevent="submitUpdate()" class="space-y-5">
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Judul Slide</label>
                                <input type="text" name="title" x-model="editingBanner.title" required
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Subjudul</label>
                                <input type="text" name="subtitle" x-model="editingBanner.subtitle"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm">
                            </div>
                        </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Badge Kategori</label>
                        <select name="category_badge" x-model="editingBanner.category_badge" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22%239CA3AF%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')" @change="fetchContents($event.target.value)">
                            <option value="DESTINASI">Destinasi</option>
                            <option value="EVENT">Event</option>
                            <option value="BERITA_PROMOSI">Berita & Promosi</option>
                            <option value="BUDAYA">Budaya</option>
                        </select>
                    </div>

                    <!-- Konten Terkait -->
                    <div class="space-y-1.5" x-show="contentsList.length > 0 || contentLoading" x-transition>
                        <label class="block text-sm font-medium text-gray-700">Tautkan ke Konten (Opsional)</label>
                        <select name="content_id" x-model="editingBanner.content_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#6349A5]/20 focus:border-[#6349A5] outline-none transition-all text-sm" :disabled="contentLoading">
                            <option value="">Tidak ditautkan...</option>
                            <template x-for="content in contentsList" :key="content.id">
                                <option :value="content.id" x-text="content.title"></option>
                            </template>
                        </select>
                        <span x-show="contentLoading" class="text-xs text-gray-500 animate-pulse">Memuat konten...</span>
                        
                        <input type="hidden" name="content_type" :value="selectedCategory === 'BERITA_PROMOSI' ? 'berita_promosi' : (selectedCategory ? selectedCategory.toLowerCase() : '')">
                    </div>

                        <!-- Current Image Preview -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">Gambar Saat Ini</label>
                            <template x-if="editingBanner?.image_url">
                                <div class="relative rounded-2xl overflow-hidden bg-gray-100 h-32 w-full border border-gray-100 mb-3 group cursor-pointer" @click="lightboxImage = (editingBanner.image_url.startsWith('http') ? editingBanner.image_url : '/storage/' + editingBanner.image_url); showLightbox = true" title="Klik untuk memperbesar">
                                    <img :src="editingBanner.image_url.startsWith('http') ? editingBanner.image_url : '/storage/' + editingBanner.image_url" class="w-full h-full object-cover" alt="Gambar Saat Ini">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-xl">Gambar Saat Ini (Klik untuk memperbesar)</span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">Ganti Gambar Background</label>
                            <div class="relative group">
                                <input type="file" name="image_url" id="image_edit" accept="image/*" class="hidden"
                                    @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                <label for="image_edit"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#6349A5]/30 rounded-xl cursor-pointer hover:bg-gray-50 transition-all bg-[#F8F7FA]">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-6 h-6 text-[#6349A5] mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        <p class="text-sm font-bold text-[#6349A5]"
                                            x-text="fileName || 'Klik atau seret file baru ke sini'"></p>
                                        <p class="text-xs text-gray-400 mt-1">Biarkan kosong jika tetap</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Periode Dari</label>
                                <input type="date" name="start_date" x-model="editingBanner.start_date_formatted"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none transition-all text-sm focus:ring-[#6349A5]">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Periode Sampai</label>
                                <input type="date" name="end_date" x-model="editingBanner.end_date_formatted"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none transition-all text-sm focus:ring-[#6349A5]">
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
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#6349A5]">
                                </div>
                            </label>
                        </div>

                        <div class="pt-2 flex justify-end gap-3 border-t border-gray-100 pt-5">
                            <button type="button" @click="showEditModal = false"
                                class="px-6 py-2.5 text-sm font-extrabold text-gray-600 hover:text-gray-800 transition-colors border border-gray-200 rounded-lg bg-white">Batal</button>
                            <button type="submit"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-[#6349A5] hover:bg-[#523A91] rounded-lg shadow-md transition-all flex items-center gap-2"
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
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan urutan: ' + result.message);
                }
            } catch (e) {
                console.error('Failed to sort', e);
                alert('Terjadi kesalahan saat menyimpan urutan.');
            } finally {
                this.loadingOrder = false;
            }
        },

        init() {
            this.initSortable();
            // Start autoplay for live preview
            if (this.activeBanners.length > 0) {
                setInterval(() => {
                    this.nextPreview();
                }, 3500);
            }
        },

        showEditModal: false,
        showCreateModal: false,
        editingBanner: {},
        loading: false,
        fileName: '',
        createFileName: '',
        showLightbox: false,
        lightboxImage: '',
        contentsList: [],
        selectedCategory: '',
        contentLoading: false,

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
            this.editingBanner = {};
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
                
                if (this.editingBanner.category_badge) {
                    await this.fetchContents(this.editingBanner.category_badge);
                }
            } catch (error) {
                alert('Gagal mengambil data banner');
                this.showEditModal = false;
            } finally {
                this.loading = false;
            }
        },
        
        async submitUpdate() {
            const bannerId = this.editingBanner._id || this.editingBanner.id;
            if (!bannerId) {
                alert('ID Banner tidak ditemukan');
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
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal memperbarui banner');
                }
            } catch (error) {
                console.error(error);
                alert(error.message || 'Terjadi kesalahan saat menyimpan data');
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
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal membuat banner');
                }
            } catch (error) {
                console.error(error);
                alert(error.message || 'Terjadi kesalahan saat menyimpan data');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
