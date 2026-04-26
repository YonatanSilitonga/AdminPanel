@extends('admin.layouts.app')

@section('title', 'Trending Destinasi')
@section('navbar_title', 'Trending Destinasi')
@section('page_title', 'Trending Destinasi')
@section('page_description', 'Analisis dan kelola destinasi yang sedang populer di aplikasi')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </span>
    <span class="text-gray-900 font-bold">Trending Destinasi</span>
</nav>
@endsection

@section('content')
<div x-data="trendingManager()" x-init="init()" class="pb-10">
    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Stat 1 -->
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

        <!-- Stat 2 -->
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

        <!-- Stat 3 -->
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

    <!-- Main Chart Section -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Tren Pencarian Destinasi — 7 Hari Terakhir</h3>
        <div class="h-80 w-full">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Performa Destinasi Top 5</h3>
            <div class="h-80 w-full">
                <canvas id="top5Chart"></canvas>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Demografis Pengunjung</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="demoChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-2xl font-bold text-gray-800">100%</span>
                </div>
            </div>
            <div class="mt-8 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                        <span class="text-sm text-gray-600">Keluarga</span>
                    </div>
                    <span class="text-sm font-bold text-gray-800">42%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-sm text-gray-600">Solo Travel</span>
                    </div>
                    <span class="text-sm font-bold text-gray-800">28%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-sm text-gray-600">Lainnya</span>
                    </div>
                    <span class="text-sm font-bold text-gray-800">30%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration & Management -->
    <div class="bg-teal-50/40 p-6 rounded-[2rem] border border-teal-100 mb-8">
        <div class="flex items-start gap-4">
            <div class="p-2 bg-teal-100 text-teal-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="font-bold text-teal-900 mb-1 text-sm">Cara Kerja Trending</h4>
                    <p class="text-xs text-teal-700 leading-relaxed">
                        <strong class="text-teal-900">Mode Otomatis:</strong> Sistem menentukan trending berdasarkan jumlah tayangan + wishlist + trip dalam 7 hari terakhir.
                    </p>
                </div>
                <div>
                    <p class="text-xs text-teal-700 leading-relaxed mt-5">
                        <strong class="text-teal-900">Mode Manual:</strong> Admin menentukan sendiri urutan destinasi trending yang ditampilkan di aplikasi mobile.
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-6 pt-6 border-t border-teal-100 flex items-center gap-4">
            <span class="text-xs font-bold text-teal-900 uppercase tracking-wider">Mode Aktif:</span>
            <div class="flex bg-white p-1 rounded-xl shadow-sm border border-teal-100">
                <button @click="setMode('manual')" :class="mode === 'manual' ? 'bg-sidebar text-white shadow-md' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[10px] font-bold transition-all">
                    Manual
                </button>
                <button @click="setMode('automatic')" :class="mode === 'automatic' ? 'bg-sidebar text-white shadow-md' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[10px] font-bold transition-all">
                    Otomatis
                </button>
            </div>
        </div>
    </div>

    <!-- Management List & Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Manual Management -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Urutan Trending di Aplikasi Mobile</h3>
                        <p class="text-xs text-gray-400 mt-1" x-show="mode === 'manual'">Drag & drop untuk mengubah urutan</p>
                        <p class="text-xs text-teal-500 mt-1 font-medium" x-show="mode === 'automatic'">Mode Otomatis Aktif (Hanya Baca)</p>
                    </div>
                    <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                        <span x-text="trendingList.length"></span>/10 Destinasi
                    </span>
                </div>

                <div class="space-y-3" id="trending-sortable">
                    <template x-for="(item, index) in trendingList" :key="item.id_str || (item._id && item._id.$oid) || item._id">
                        <div class="flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-2xl hover:shadow-md transition-all group" :data-id="item.id_str || (item._id && item._id.$oid) || item._id">
                            <div class="text-gray-300 hover:text-gray-500 drag-handle" :class="mode === 'manual' ? 'cursor-grab' : 'opacity-50 cursor-not-allowed'" x-show="mode === 'manual'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-sidebar flex items-center justify-center text-white text-[10px] font-bold" x-text="index + 1"></div>
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                                <img :src="item.images && item.images[0] ? '/storage/' + item.images[0] : 'https://images.unsplash.com/photo-1542332213-9b5a5a3fad35?auto=format&fit=crop&w=100&q=80'" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-800 text-sm truncate" x-text="item.name"></h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-gray-400 capitalize" x-text="item.category"></span>
                                    <span class="text-gray-300">•</span>
                                    <div class="flex items-center text-yellow-500 gap-0.5">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        <span class="text-[10px] font-bold" x-text="item.average_rating || '4.5'"></span>
                                    </div>
                                </div>
                            </div>
                            <button x-show="mode === 'manual'" @click="removeItem(item.id_str || (item._id && item._id.$oid) || item._id)" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors border border-transparent hover:border-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-50" x-show="mode === 'manual'">
                    <h4 class="font-bold text-gray-800 mb-4 text-sm">Tambah Destinasi ke Trending</h4>
                    <div class="relative" @click.away="searchResults = []">
                        <div class="relative">
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="searchDestinations()" @keydown.enter.prevent="addFirstResult()" placeholder="Cari destinasi aktif..." class="w-full pl-11 pr-24 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sidebar/5 focus:border-sidebar outline-none transition-all text-sm font-medium">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <button @click="addFirstResult()" class="px-4 py-2 bg-sidebar text-white rounded-xl text-xs font-bold hover:bg-sidebar-hover transition-colors shadow-lg shadow-sidebar/20">
                                    Tambah
                                </button>
                            </div>
                        </div>

                        <div x-show="searchResults.length > 0" class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-1">
                            <template x-for="res in searchResults" :key="res.id_str || (res._id && res._id.$oid) || res._id">
                                <div @click="addItem(res)" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors border-b border-gray-50 last:border-0">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0">
                                        <img :src="res.images && res.images[0] ? '/storage/' + res.images[0] : 'https://images.unsplash.com/photo-1542332213-9b5a5a3fad35?auto=format&fit=crop&w=100&q=80'" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-800 text-sm" x-text="res.name"></p>
                                        <p class="text-[10px] text-gray-400 truncate" x-text="res.location"></p>
                                    </div>
                                    <div class="p-1.5 bg-sidebar/10 text-sidebar rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-4" x-show="mode === 'manual'">
                    <button @click="saveOrder()" class="flex-1 py-4 bg-sidebar text-white rounded-2xl font-bold hover:bg-sidebar-hover transition-all shadow-lg shadow-sidebar/20 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Urutan Trending
                    </button>
                    <button @click="resetToAuto()" class="px-8 py-4 bg-white border border-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="lg:col-span-1 sticky top-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest text-[10px]">Mobile Preview</h3>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-600 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[9px] font-bold uppercase tracking-wider">Live</span>
                    </div>
                </div>

                <div class="relative mx-auto w-[220px] h-[440px] bg-white border-[6px] border-gray-800 rounded-[2.5rem] shadow-2xl overflow-hidden shadow-gray-200">
                    <div class="absolute top-0 inset-x-0 h-4 bg-gray-800 rounded-b-xl w-20 mx-auto z-20"></div>
                    
                    <div class="h-full bg-gray-50 w-full overflow-hidden pt-8 px-3 pb-8">
                        <h4 class="text-[9px] font-bold text-gray-900 mb-3 uppercase tracking-wider">Trending</h4>
                        
                        <div class="space-y-3">
                            <template x-for="(item, i) in trendingList.slice(0, 4)" :key="item.id_str || (item._id && item._id.$oid) || item._id">
                                <div class="relative w-full h-24 rounded-xl overflow-hidden shadow-sm">
                                    <img :src="item.images && item.images[0] ? '/storage/' + item.images[0] : 'https://images.unsplash.com/photo-1542332213-9b5a5a3fad35?auto=format&fit=crop&w=400&q=80'" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                    <div class="absolute top-2 left-2 w-4 h-4 bg-sidebar text-white rounded flex items-center justify-center text-[8px] font-bold border border-white/20" x-text="i + 1"></div>
                                    <div class="absolute bottom-2 left-2 pr-2">
                                        <h5 class="text-white font-bold text-[9px] leading-tight truncate" x-text="item.name"></h5>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <svg class="w-2 h-2 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            <span class="text-[7px] text-white/80" x-text="item.average_rating || '4.5'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Modals -->
    <div x-show="showSuccessModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center transform transition-all">
            <div :class="modalType === 'error' ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500'" class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <template x-if="modalType === 'success'">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="modalType === 'error'">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </template>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2" x-text="modalTitle"></h3>
            <p class="text-gray-500 mb-8" x-text="successMessage"></p>
            <button @click="showSuccessModal = false" :class="modalType === 'error' ? 'bg-red-500 hover:bg-red-600' : 'bg-sidebar hover:bg-sidebar-hover'" class="w-full py-4 text-white rounded-2xl font-bold transition-all shadow-lg">
                Selesai
            </button>
        </div>
    </div>

    <div x-show="showConfirmModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center transform transition-all">
            <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi</h3>
            <p class="text-gray-500 mb-8" x-text="confirmMessage"></p>
            <div class="flex gap-3">
                <button @click="showConfirmModal = false" class="flex-1 py-4 bg-gray-50 text-gray-500 rounded-2xl font-bold hover:bg-gray-100 transition-all">
                    Batal
                </button>
                <button @click="executeConfirm()" class="flex-1 py-4 bg-sidebar text-white rounded-2xl font-bold hover:bg-sidebar-hover transition-all shadow-lg shadow-sidebar/20">
                    Ya, Reset
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function trendingManager() {
    return {
        mode: '{{ $mode }}',
        trendingList: @json($trendingDestinations),
        searchQuery: '',
        searchResults: [],
        loading: false,
        
        showSuccessModal: false,
        successMessage: '',
        modalTitle: '',
        modalType: 'success',
        showConfirmModal: false,
        confirmMessage: '',
        confirmAction: null,

        init() {
            this.initCharts();
            this.initSortable();
        },

        initSortable() {
            const el = document.getElementById('trending-sortable');
            if (el) {
                Sortable.create(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'opacity-50',
                    draggable: '[data-id]', // Hanya item dengan data-id yang dihitung
                    onEnd: (evt) => {
                        // 1. Dapatkan index yang jauh lebih akurat (mengabaikan <template>)
                        const oldIdx = evt.oldDraggableIndex;
                        const newIdx = evt.newDraggableIndex;

                        if (oldIdx !== undefined && newIdx !== undefined && oldIdx !== newIdx) {
                            // 2. Batalkan sementara perubahan DOM fisik
                            const item = evt.item;
                            const parent = item.parentNode;
                            parent.removeChild(item); 
                            
                            const referenceNode = parent.children[evt.oldIndex];
                            if (referenceNode) {
                                parent.insertBefore(item, referenceNode);
                            } else {
                                parent.appendChild(item);
                            }

                            // 3. Paksa Alpine merender dengan Re-Assignment Array Baru!
                            const newList = [...this.trendingList];
                            const movedItem = newList.splice(oldIdx, 1)[0];
                            newList.splice(newIdx, 0, movedItem);
                            
                            // Re-assign untuk memicu reactivity
                            this.trendingList = newList;
                        }
                    }
                });
            }
        },

        async setMode(mode) {
            try {
                const res = await fetch('{{ route("admin.trending.update-mode") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ mode })
                });
                const data = await res.json();
                if (data.success) {
                    this.mode = mode;
                    this.showModal('Berhasil!', 'Mode trending diperbarui ke ' + mode);
                }
            } catch (e) {
                this.showModal('Ups! Gagal', 'Gagal mengubah mode', 'error');
            }
        },

        async searchDestinations() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }
            try {
                const res = await fetch(`{{ route("admin.trending.search") }}?q=${this.searchQuery}`);
                this.searchResults = await res.json();
            } catch (e) {
                console.error(e);
            }
        },

        addFirstResult() {
            if (this.searchResults.length > 0) {
                this.addItem(this.searchResults[0]);
            } else if (this.searchQuery.length > 0) {
                this.showModal('Info', 'Tidak ditemukan destinasi dengan nama tersebut', 'error');
            }
        },

        addItem(item) {
            const id = item._id || item.id_str;
            if (this.trendingList.some(d => (d._id === id || d.id_str === id))) {
                this.showModal('Sudah Ada', 'Destinasi sudah ada di daftar trending', 'error');
                return;
            }
            if (this.trendingList.length >= 10) {
                this.showModal('Limit Tercapai', 'Maksimal 10 destinasi trending', 'error');
                return;
            }
            this.trendingList.push(item);
            this.searchResults = [];
            this.searchQuery = '';
        },

        removeItem(id) {
            this.trendingList = this.trendingList.filter(d => (d._id !== id && d.id_str !== id));
        },

        async saveOrder() {
            const orders = this.trendingList.map(d => d.id_str || (d._id && d._id.$oid) || d._id);
            try {
                const res = await fetch('{{ route("admin.trending.update-order") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ orders })
                });
                const data = await res.json();
                if (data.success) {
                    this.showModal('Berhasil!', 'Urutan trending berhasil disimpan!');
                } else {
                    this.showModal('Gagal', data.message || 'Gagal menyimpan', 'error');
                }
            } catch (e) {
                this.showModal('Ups! Gagal', 'Gagal menyimpan urutan', 'error');
            }
        },

        async resetToAuto() {
            this.confirm('Yakin ingin mengembalikan ke mode otomatis?', async () => {
                try {
                    const res = await fetch('{{ route("admin.trending.reset") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.location.reload();
                    }
                } catch (e) {
                    this.showModal('Gagal', 'Gagal melakukan reset', 'error');
                }
            });
        },

        showModal(title, message, type = 'success') {
            this.modalTitle = title;
            this.successMessage = message;
            this.modalType = type;
            this.showSuccessModal = true;
        },

        confirm(message, action) {
            this.confirmMessage = message;
            this.confirmAction = action;
            this.showConfirmModal = true;
        },

        executeConfirm() {
            if (this.confirmAction) this.confirmAction();
            this.showConfirmModal = false;
        },

        initCharts() {
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        data: [4500, 5200, 4800, 5900, 6800, 8200, 7842],
                        borderColor: '#066466',
                        backgroundColor: 'rgba(6, 100, 102, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { display: false }, x: { grid: { display: false } } }
                }
            });

            const top5Ctx = document.getElementById('top5Chart').getContext('2d');
            new Chart(top5Ctx, {
                type: 'bar',
                data: {
                    labels: ['P. Bulbul', 'B. Tarabunga', 'M. Batak', 'A.T Situmurun', 'Balige'],
                    datasets: [
                        { label: 'View', data: [85, 72, 65, 55, 45], backgroundColor: '#066466', borderRadius: 6 },
                        { label: 'Wish', data: [35, 28, 22, 18, 15], backgroundColor: '#10B981', borderRadius: 6 },
                        { label: 'Review', data: [15, 12, 10, 8, 5], backgroundColor: '#F59E0B', borderRadius: 6 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            const demoCtx = document.getElementById('demoChart').getContext('2d');
            new Chart(demoCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Keluarga', 'Solo', 'Lainnya'],
                    datasets: [{
                        data: [42, 28, 30],
                        backgroundColor: ['#0D9488', '#10B981', '#F59E0B'],
                        borderWidth: 0
                    }]
                },
                options: { cutout: '80%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }
    }
}
</script>
@endpush

<style>
    [x-cloak] { display: none !important; }
    .cursor-grab { cursor: grab; }
    .cursor-grab:active { cursor: grabbing; }
    .sticky { position: sticky; }
</style>
@endsection
