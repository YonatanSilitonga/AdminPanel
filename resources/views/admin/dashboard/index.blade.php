@extends('admin.layouts.app')

@section('title', 'Beranda')
@section('navbar_title', 'Beranda')
@section('page_title', 'Beranda')

@section('content')
<!-- Header Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Destinasi -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-sm font-medium text-gray-500">Destinasi</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah total destinasi pariwisata yang terdaftar di dalam sistem.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Dashboard Utama untuk pemantauan kapasitas data konten pariwisata.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_destinations'] ?? 0 }}</p>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-purple-600 font-medium">Total Destinasi</span>
        </div>
    </div>

    <!-- Card 2: Event Aktif -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-sm font-medium text-gray-500">Event</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah total event pariwisata atau kegiatan yang terdaftar.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Dashboard Utama untuk pemantauan agenda kegiatan wisata.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_events'] ?? 0 }}</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">Total Event</span>
        </div>
    </div>

    <!-- Card 3: Pengguna -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-sm font-medium text-gray-500">Pengguna</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah total pengguna (wisatawan) terdaftar pada sistem.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Dashboard Utama untuk memantau pertumbuhan basis pengguna.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_users'] ?? 0) }}</p>
            </div>
            <div class="p-3 bg-orange-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-orange-500 font-medium">Total Pengguna</span>
        </div>
    </div>

    <!-- Card 4: Laporan -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-sm font-medium text-gray-500">Laporan Pending</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah laporan pengaduan dari wisatawan yang statusnya masih pending dan perlu ditindaklanjuti.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Dashboard Utama sebagai pengingat aksi moderasi admin.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $pendingReports ?? 0 }}</p>
            </div>
            <div class="p-3 bg-red-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-red-500 font-medium">Belum Ditangani</span>
        </div>
    </div>
</div>


<!-- Middle Section: Chart and Activity Timeline -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <!-- Chart -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 xl:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-1.5">
                <h2 class="text-lg font-bold text-gray-900">Aktivitas Bulanan</h2>
                <div class="relative group cursor-pointer inline-flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                        <div class="space-y-2">
                            <div>
                                <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                <p class="text-slate-200 font-normal">Memvisualisasikan tren pembuatan konten dan aktivitas di sistem secara grafis.</p>
                            </div>
                            <div class="pt-1.5 border-t border-slate-800">
                                <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                <p class="text-slate-200 font-normal">Dashboard Utama sebagai representasi visual data tahun berjalan.</p>
                            </div>
                        </div>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-2 text-[11px] font-bold text-gray-500">
                <div class="flex items-center"><span class="w-3 h-0.5 bg-purple-600 mr-2 rounded-full"></span>Destinasi</div>
                <div class="flex items-center"><span class="w-3 h-0.5 bg-green-600 mr-2 rounded-full"></span>Event</div>
                <div class="flex items-center"><span class="w-3 h-0.5 bg-blue-500 mr-2 rounded-full"></span>Berita</div>
                <div class="flex items-center"><span class="w-3 h-0.5 bg-orange-500 mr-2 rounded-full"></span>Budaya</div>
                <div class="flex items-center"><span class="w-3 h-0.5 bg-pink-500 mr-2 rounded-full"></span>Fasilitas</div>
                <div class="flex items-center"><span class="w-3 h-0.5 bg-teal-500 mr-2 rounded-full"></span>Ulasan</div>
                <div class="flex items-center"><span class="w-3 h-0.5 bg-red-500 mr-2 rounded-full"></span>Laporan</div>
            </div>
        </div>
        <div class="relative h-64 md:h-72 w-full mt-6">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Activity -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col h-full">
        <div class="flex items-center gap-1.5 mb-6 shrink-0">
            <h2 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h2>
            <div class="relative group cursor-pointer inline-flex items-center">
                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                    <div class="space-y-2">
                        <div>
                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                            <p class="text-slate-200 font-normal">Melacak secara realtime log aktivitas atau tindakan yang dilakukan oleh seluruh administrator.</p>
                        </div>
                        <div class="pt-1.5 border-t border-slate-800">
                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                            <p class="text-slate-200 font-normal">Dashboard Utama untuk pemantauan keamanan operasional harian.</p>
                        </div>
                    </div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                </div>
            </div>
        </div>
        <div class="relative pl-6 border-l-2 border-gray-100 space-y-6 flex-1 overflow-y-auto custom-scrollbar pr-2 max-h-[290px]">
        @forelse(($recentActivity ?? []) as $index => $log)
            <div class="relative">
                <div class="absolute -left-[33px] bg-white p-1 rounded-full">
                    @php
                        $colors = ['bg-green-600', 'bg-red-500', 'bg-yellow-500', 'bg-purple-600', 'bg-blue-500'];
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="w-2.5 h-2.5 {{ $color }} rounded-full"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $log->action ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ optional($log->admin)->name ?? '-' }}
                        · {{ optional($log->created_at)->diffForHumans() ?? '-' }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
        @endforelse
        </div>
    </div>
</div>

<!-- Bottom Section: 3 Columns -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8 mt-2">
    <!-- Laporan Terbaru (Replacement for Featured) -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 h-full flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h2 class="text-lg font-bold text-gray-900">Statistik Sistem</h2>
            </div>
            <div class="relative group cursor-pointer inline-flex items-center">
                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-3 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                    <div class="space-y-2">
                        <div>
                            <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                            <p class="text-slate-200 font-normal">Menampilkan rangkuman statistik data ulasan, fasilitas, berita, dan budaya.</p>
                        </div>
                        <div class="pt-1.5 border-t border-slate-800">
                            <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                            <p class="text-slate-200 font-normal">Dashboard Utama sebagai ikhtisar volume data pendukung aplikasi.</p>
                        </div>
                    </div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">Total Ulasan</span>
                <span class="text-sm font-bold text-gray-900">{{ number_format($stats['total_reviews'] ?? 0) }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">Total Fasilitas</span>
                <span class="text-sm font-bold text-gray-900">{{ number_format($stats['total_facilities'] ?? 0) }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">Total Berita & Promo</span>
                <span class="text-sm font-bold text-gray-900">{{ number_format($stats['total_berita'] ?? 0) }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">Total Budaya & Warisan</span>
                <span class="text-sm font-bold text-gray-900">{{ number_format($stats['total_budaya'] ?? 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Top 5 Destinasi Terbaik -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 h-full flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900">Top 5 Destinasi Terbaik</h2>
            <div class="relative group cursor-pointer inline-flex items-center">
                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                    <div class="space-y-2">
                        <div>
                            <span class="block font-bold text-yellow-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                            <p class="text-slate-200 font-normal">Menampilkan lima destinasi wisata dengan rata-rata rating ulasan tertinggi.</p>
                        </div>
                        <div class="pt-1.5 border-t border-slate-800">
                            <span class="block font-bold text-yellow-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                            <p class="text-slate-200 font-normal">Dashboard Utama untuk memantau performa destinasi terpopuler.</p>
                        </div>
                    </div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            @forelse($topDestinations ?? [] as $index => $dest)
            <div class="flex items-center gap-3 bg-gray-50 p-2.5 rounded-2xl hover:bg-gray-100 transition-colors">
                <span class="text-gray-400 text-sm w-5 font-bold text-center">{{ $index + 1 }}</span>
                <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-200 shrink-0">
                    @if(isset($dest->images) && count($dest->images) > 0)
                        <img src="{{ image_url($dest->images[0]) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate" title="{{ $dest->name }}">{{ $dest->name }}</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <span class="text-[11px] font-bold text-gray-600">{{ number_format($dest->average_rating ?? 0, 1) }}</span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">Belum ada data destinasi.</p>
            @endforelse
        </div>
    </div>

    <!-- Trip Dibuat -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 h-full flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <h2 class="text-lg font-bold text-gray-900">Trip Dibuat</h2>
            </div>
            <div class="relative group cursor-pointer inline-flex items-center">
                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                    <div class="space-y-2">
                        <div>
                            <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                            <p class="text-slate-200 font-normal">Memantau jumlah pembuatan rencana perjalanan (trip planner) oleh wisatawan secara berkala (hari, minggu, bulan ini).</p>
                        </div>
                        <div class="pt-1.5 border-t border-slate-800">
                            <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                            <p class="text-slate-200 font-normal">Dashboard Utama untuk memantau keaktifan wisatawan menggunakan fitur trip planner.</p>
                        </div>
                    </div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                </div>
            </div>
        </div>
        <div class="space-y-6 mt-4">
            <!-- Hari ini -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500">Hari ini</span>
                    <span class="text-sm font-bold text-gray-900">18</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-purple-600 h-2.5 rounded-full" style="width: 15%"></div>
                </div>
            </div>
            <!-- Minggu ini -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500">Minggu ini</span>
                    <span class="text-sm font-bold text-gray-900">94</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 45%"></div>
                </div>
            </div>
            <!-- Bulan ini -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500">Bulan ini</span>
                    <span class="text-sm font-bold text-gray-900">312</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 85%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('charts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;

        // Function to initialize chart with data
        const initChart = (chartData) => {
            const labels = chartData.map(item => item.month ?? '-');
            const destinations = chartData.map(item => item.destinations ?? 0);
            const events = chartData.map(item => item.events ?? 0);
            const berita = chartData.map(item => item.berita ?? 0);
            const budaya = chartData.map(item => item.budaya ?? 0);
            const fasilitas = chartData.map(item => item.fasilitas ?? 0);
            const reviews = chartData.map(item => item.reviews ?? 0);
            const reports = chartData.map(item => item.reports ?? 0);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Destinasi',
                            data: destinations.length ? destinations : [0, 0, 0, 0, 0, 0],
                            borderColor: '#9333ea', // purple-600
                            backgroundColor: '#9333ea',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            fill: false
                        },
                        {
                            label: 'Event',
                            data: events.length ? events : [0, 0, 0, 0, 0, 0],
                            borderColor: '#16a34a', // green-600
                            backgroundColor: '#16a34a',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            fill: false
                        },
                        {
                            label: 'Berita',
                            data: berita.length ? berita : [0, 0, 0, 0, 0, 0],
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: '#3b82f6',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            fill: false
                        },
                        {
                            label: 'Budaya',
                            data: budaya.length ? budaya : [0, 0, 0, 0, 0, 0],
                            borderColor: '#f97316', // orange-500
                            backgroundColor: '#f97316',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            fill: false
                        },
                        {
                            label: 'Fasilitas',
                            data: fasilitas.length ? fasilitas : [0, 0, 0, 0, 0, 0],
                            borderColor: '#ec4899', // pink-500
                            backgroundColor: '#ec4899',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            fill: false
                        },
                        {
                            label: 'Ulasan',
                            data: reviews.length ? reviews : [0, 0, 0, 0, 0, 0],
                            borderColor: '#14b8a6', // teal-500
                            backgroundColor: '#14b8a6',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            fill: false
                        },
                        {
                            label: 'Laporan',
                            data: reports.length ? reports : [0, 0, 0, 0, 0, 0],
                            borderColor: '#ef4444', // red-500
                            backgroundColor: '#ef4444',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            display: false,
                            min: 0,
                        },
                        x: {
                            grid: {
                                color: '#f3f4f6', // gray-100
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#9ca3af', // gray-400
                                font: { size: 12 }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        };

        // Fetch chart data via AJAX
        fetch('{{ route("admin.dashboard.chart-data") }}')
            .then(response => response.json())
            .then(data => {
                initChart(data);
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                initChart([]); // Show empty chart on error
            });
    });
</script>
@endpush
@endsection
