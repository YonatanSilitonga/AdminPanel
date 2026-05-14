@extends('admin.layouts.app')

@section('title', 'Recommendation Detail')
@section('navbar_title', 'Detail Rencana Trip')
@section('page_title', 'Detail Rencana Trip')
@section('page_description', 'Informasi lengkap rekomendasi dan riwayat trip planner yang dibuat sistem AI')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring AI</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <a href="{{ route('admin.recommendations.index') }}" class="text-gray-400 hover:text-emerald-600 transition-colors">Recommendation Log</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Detail Rencana</span>
</nav>
@endsection

@section('content')

@php
    $tripId = '#TRP-2024-' . substr($log->_id, -3);
    $destination = $log->destination?->name ?? 'N/A';
    $duration = round($log->recommendation_score);
@endphp

<!-- Recommendation Info Header -->
<div class="bg-white rounded-[20px] border border-gray-100 p-8 mb-8 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-8">
        <div class="flex items-center gap-6">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
            </div>
            <div>
                <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Trip ID</p>
                <p class="font-mono text-[15px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg inline-block">{{ $tripId }}</p>
            </div>
        </div>

        <div class="h-12 w-px bg-gray-100 hidden md:block"></div>

        <div>
            <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Durasi Trip</p>
            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[12px] font-bold rounded-xl uppercase tracking-wider">{{ $duration }} Hari</span>
        </div>

        <div class="h-12 w-px bg-gray-100 hidden md:block"></div>

        <div>
            <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Dibuat Tanggal</p>
            <p class="text-[15px] font-bold text-gray-800">
                {{ $log->created_at->format('d M Y, H:i') }} WIB
            </p>
        </div>

        <div class="flex items-center gap-3 ml-auto">
            <a href="{{ route('admin.recommendations.index') }}" class="px-6 py-3 bg-gray-50 text-gray-500 rounded-xl text-[14px] font-bold hover:bg-gray-100 transition-all border border-transparent hover:border-gray-200">
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Itinerary & Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Destination Hero -->
        <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="h-64 bg-emerald-700 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220%200%2060%2060%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.4%22%3E%3Cpath d=%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div class="absolute bottom-8 left-8">
                    <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md text-white text-[11px] font-bold rounded-xl uppercase tracking-wider mb-3 inline-block">Destinasi Utama</span>
                    <h2 class="text-3xl font-bold text-white">{{ $destination }}</h2>
                </div>
            </div>
            <div class="p-8">
                <p class="text-gray-600 leading-relaxed text-[15px]">
                    Destinasi ini dipilih berdasarkan kecocokan preferensi pengguna dengan skor rekomendasi yang tinggi. 
                    Trip planner ini mencakup rute optimal untuk memaksimalkan waktu kunjungan di kawasan wisata Sumatera Utara.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-6 p-6 bg-gray-50/50 rounded-2xl border border-gray-100">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rating Populer</p>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-amber-500">4.8</span>
                            <div class="flex gap-0.5">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Total Kunjungan</p>
                        <p class="text-2xl font-bold text-gray-800">1,284 <span class="text-xs text-gray-400 font-medium ml-1">Reviews</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itinerary List -->
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-8">Rencana Perjalanan Terperinci</h3>
            
            <div class="space-y-12">
                @for($day = 1; $day <= 3; $day++)
                <div class="flex gap-6 relative">
                    @if($day < 3)
                        <div class="absolute left-6 top-14 bottom-[-48px] w-0.5 bg-emerald-50"></div>
                    @endif
                    <div class="relative z-10 w-12 h-12 bg-emerald-700 rounded-2xl flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-700/20 shrink-0">
                        {{ $day }}
                    </div>
                    <div class="flex-grow pt-1">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">
                            Hari Ke-{{ $day }}: 
                            @if($day == 1) Wisata Alam & Kedatangan
                            @elseif($day == 2) Eksplorasi Budaya Lokal
                            @else Relaksasi & Oleh-oleh @endif
                        </h4>
                        <p class="text-gray-500 text-[14px] leading-relaxed mb-4">
                            @if($day == 1) Perjalanan dimulai dengan kunjungan ke {{ $destination }} untuk menikmati udara segar dan pemandangan Danau Toba.
                            @elseif($day == 2) Menjelajahi situs bersejarah dan museum untuk memahami warisan budaya Batak Toba yang kaya.
                            @else Menikmati waktu santai sebelum kembali pulang dan membeli beberapa kerajinan khas lokal. @endif
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-xl border border-gray-100/50">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-[13px] font-bold text-gray-600">Aktivitas Utama</span>
                            </div>
                            <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-xl border border-gray-100/50">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="text-[13px] font-bold text-gray-600">Rekomendasi Kuliner</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-8">
        <!-- User Context -->
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Konteks Pengguna</h4>
            
            <div class="space-y-6">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Pengguna</p>
                    <p class="text-[15px] font-bold text-gray-800">{{ optional($log->behavior_data)['user_name'] ?? 'Guest User / Tamu' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">User ID</p>
                    <p class="text-[12px] font-mono text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg break-all border border-emerald-100/50">
                        {{ $log->user_id ?? 'ANONYMOUS_SESSION' }}
                    </p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Minat & Preferensi</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-gray-50 text-gray-600 text-[11px] font-bold rounded-lg border border-gray-100">Alam</span>
                        <span class="px-3 py-1 bg-gray-50 text-gray-600 text-[11px] font-bold rounded-lg border border-gray-100">Budaya</span>
                        <span class="px-3 py-1 bg-gray-50 text-gray-600 text-[11px] font-bold rounded-lg border border-gray-100">Fotografi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Score -->
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Analisis Sistem</h4>
            
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Skor Relevansi</p>
                        <p class="text-[13px] font-bold text-emerald-700">{{ round($log->recommendation_score * 10) }}%</p>
                    </div>
                    <div class="w-full bg-gray-50 rounded-full h-2.5 overflow-hidden border border-gray-100">
                        <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $log->recommendation_score * 10 }}%"></div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Status Konversi</p>
                    @if($log->is_clicked)
                        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[13px] font-bold">Rencana Diklik</span>
                        </div>
                    @else
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 text-gray-400 rounded-xl border border-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[13px] font-bold">Belum Ada Aksi</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Footer -->
        <div class="bg-emerald-700 rounded-[20px] p-8 shadow-lg shadow-emerald-700/20 text-white relative overflow-hidden">
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            <h4 class="text-sm font-bold mb-2 relative z-10">AI Insights</h4>
            <p class="text-emerald-100 text-[13px] leading-relaxed relative z-10">Data ini membuktikan bahwa preferensi "Alam & Budaya" memiliki tingkat konversi yang tinggi di kawasan Toba.</p>
        </div>
    </div>
</div>

@endsection
