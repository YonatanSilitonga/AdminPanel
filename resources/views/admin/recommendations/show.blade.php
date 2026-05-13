@extends('admin.layouts.app')

@section('title', 'Recommendation Detail')
@section('page_title', 'Detail Rencana Trip')
@section('page_description', 'Informasi lengkap rekomendasi dan riwayat trip planner')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Fitur AI dan Cerdas</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <a href="{{ route('admin.recommendations.index') }}" class="text-gray-400 hover:text-sidebar transition-colors">Recommendation Log</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Detail Rencana</span>
</nav>
@endsection

@section('content')

@php
    $tripId = '#TRP-2024-001';
    $destination = $log->destination?->name ?? 'N/A';
    $duration = round($log->recommendation_score);
    $isClicked = $log->is_clicked ? 'Ya' : 'Tidak';
@endphp

{{-- Back Button --}}
<a href="{{ route('admin.recommendations.index') }}" class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 mb-6 font-semibold">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
    Kembali ke Daftar
</a>

{{-- Header Info Card --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-4 gap-6">
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Trip ID</p>
            <p class="text-lg font-mono font-bold text-teal-600 mt-2">{{ $tripId }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Durasi Perjalanan</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $duration }} Hari</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Tanggal Dibuat</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $log->created_at->format('d M Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Waktu Dibuat</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $log->created_at->format('H:i') }} WIB</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Main Content --}}
    <div class="col-span-2">

        {{-- Destination Card --}}
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="h-48 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600 relative">
                <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,...');"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="p-6">
                <span class="inline-block bg-teal-100 text-teal-700 text-xs font-bold px-3 py-1 rounded-full mb-3">DESTINASI UTAMA</span>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $destination }}</h2>
                <p class="text-gray-600 leading-relaxed">
                    Pantai berpasir putih di tepiaan Danau Toba yang menjadi favorit traveler di kawasan Sumatera Utara. 
                    Destinasi ini menawarkan pemandangan alam yang spektakuler dengan berbagai aktivitas wisata yang menarik.
                </p>
                <div class="mt-6 flex gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Rating</p>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-2xl font-bold text-amber-500">4.8</span>
                            <div class="flex gap-0.5">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Reviews</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">1,284</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Itinerary Section --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Rencana Perjalanan Terperinci</h3>

            <div class="space-y-6">
                {{-- Day 1 --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold">1</div>
                        <div class="w-0.5 h-32 bg-gray-300 mt-2"></div>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-1">Hari Pertama: Wisata Alam</h4>
                        <p class="text-sm text-gray-600 mb-4">Mulai dari kedatangan di Bandara, perjalanan ke Pantai Bulbul, istirahat, dan menjelajahi area sekitar</p>
                        <div class="bg-gray-50 rounded p-3 text-sm text-gray-600">
                            <p class="font-semibold text-gray-700 mb-2">Aktivitas:</p>
                            <ul class="space-y-1 list-disc list-inside">
                                <li>Check-in Penginapan</li>
                                <li>Kunjungan Pantai Bulbul</li>
                                <li>Makan malam di restoran lokal</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Day 2 --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold">2</div>
                        <div class="w-0.5 h-32 bg-gray-300 mt-2"></div>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-1">Hari Kedua: Eksplorasi Budaya</h4>
                        <p class="text-sm text-gray-600 mb-4">Kunjungan ke museum budaya, pelajaran tentang tradisi Batak, dan interaksi dengan masyarakat lokal</p>
                        <div class="bg-gray-50 rounded p-3 text-sm text-gray-600">
                            <p class="font-semibold text-gray-700 mb-2">Aktivitas:</p>
                            <ul class="space-y-1 list-disc list-inside">
                                <li>Museum TB Silalahi Center</li>
                                <li>Danau Toba Viewing Point</li>
                                <li>Workshop tari tradisional</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Day 3 --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold">3</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-1">Hari Ketiga: Relaksasi & Kepulangan</h4>
                        <p class="text-sm text-gray-600 mb-4">Sisa pagi untuk relaksasi, kemudian perjalanan kembali menuju bandara untuk penerbangan pulang</p>
                        <div class="bg-gray-50 rounded p-3 text-sm text-gray-600">
                            <p class="font-semibold text-gray-700 mb-2">Aktivitas:</p>
                            <ul class="space-y-1 list-disc list-inside">
                                <li>Kolam renang alami</li>
                                <li>Belanja souvenir</li>
                                <li>Perjalanan ke bandara</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div>
        {{-- User Info Card --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h4 class="font-bold text-gray-800 mb-4">Informasi Pengguna</h4>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Nama Pengguna</p>
                    <p class="text-sm font-semibold text-gray-800 mt-1">{{ optional($log->behavior_data)['user_name'] ?? 'Guest User' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">User ID</p>
                    <p class="text-sm text-teal-600 mt-1 break-all font-mono text-xs">{{ $log->user_id ?? 'Anonymous' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Preferensi Kategori</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="inline-block bg-teal-100 text-teal-700 px-2 py-1 rounded text-xs">Alam & Budaya</span>
                        <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">Pantai</span>
                        <span class="inline-block bg-amber-100 text-amber-700 px-2 py-1 rounded text-xs">Kuliner</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recommendation Stats --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h4 class="font-bold text-gray-800 mb-4">Statistik Rekomendasi</h4>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Skor Rekomendasi</p>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $log->recommendation_score * 10 }}%"></div>
                        </div>
                        <p class="text-sm font-bold text-gray-800">{{ round($log->recommendation_score * 10) }}%</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Status Klik</p>
                    <div class="mt-2">
                        @if($log->is_clicked)
                            <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                ✓ Diklik Pengguna
                            </span>
                        @else
                            <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Belum Diklik
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-lg p-6 border border-teal-200">
            <h4 class="font-bold text-gray-800 mb-4">Ringkasan Trip</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Destinasi</span>
                    <span class="font-bold text-gray-800">5 Tempat</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Jarak Tempuh</span>
                    <span class="font-bold text-gray-800">~45 km</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Durasi Total</span>
                    <span class="font-bold text-gray-800">{{ $duration }} Hari</span>
                </div>
                <div class="pt-3 border-t border-teal-200 flex justify-between items-center">
                    <span class="text-sm text-gray-600">Estimasi Budget</span>
                    <span class="font-bold text-teal-600">Rp 2.5-3.5 Juta</span>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
