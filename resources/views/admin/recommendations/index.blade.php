@extends('admin.layouts.app')

@section('title', 'Recommendation Logs')
@section('navbar_title', 'Recommendation Log')
@section('page_title', 'Recommendation Log')
@section('page_description', 'Monitor dan analisis rekomendasi destinasi yang diberikan sistem AI SmartTrip')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring AI</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Recommendation Log</span>
</nav>
@endsection

@section('content')

@if(isset($error))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium">
    {{ $error }}
</div>
@endif

<!-- Stats Overview -->
<div class="bg-white rounded-[2rem] border border-gray-100 p-8 mb-8 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="flex items-center gap-4 px-6 first:pl-0">
            <div class="w-1 h-10 bg-emerald-700 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($todayLogs) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Hari Ini</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Jumlah rekomendasi itinerary yang dihasilkan AI SmartTrip untuk pengguna pada hari ini.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Sumber Data</span>
                                    <p class="text-slate-200 font-normal">MongoDB collection: recommendation_logs</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-emerald-500 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($weekLogs) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Minggu Ini</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Akumulasi rekomendasi itinerary yang dihasilkan dalam satu minggu terakhir.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-orange-400 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($monthLogs) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Bulan Ini</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Total rekomendasi itinerary yang dibuat oleh AI sepanjang bulan berjalan.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8 last:pr-0">
            <div class="w-1 h-10 bg-blue-400 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($avgDuration, 1) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Avg Durasi (Hari)</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Rata-rata durasi perjalanan (hari) yang dipilih pengguna saat menggunakan SmartTrip AI.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Field</span>
                                    <p class="text-slate-200 font-normal">recommendation_score (MongoDB)</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Distribution Chart -->
    <div class="lg:col-span-2 bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Distribusi Durasi Trip</h3>
                <p class="text-sm text-gray-400 mt-1">Preferensi durasi perjalanan dari data recommendation_logs</p>
            </div>
            <span class="px-4 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-[11px] font-bold uppercase tracking-wider">Real-time</span>
        </div>

        @php $totalDist = array_sum($distributionData ?: []); @endphp

        @if($totalDist > 0)
        <div class="space-y-6">
            @foreach($distributionData as $label => $count)
                @php
                    $percent = $totalDist > 0 ? ($count / $totalDist) * 100 : 0;
                    $colors  = ['bg-emerald-600', 'bg-emerald-500', 'bg-emerald-400'];
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[14px] font-bold text-gray-700">{{ $label }}</span>
                        <span class="text-[13px] font-bold text-emerald-700">{{ number_format($count) }} Trip ({{ round($percent) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-50 rounded-full h-2.5 overflow-hidden border border-gray-100">
                        <div class="{{ $colors[$loop->index % 3] }} h-full rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="py-10 text-center text-gray-400 text-sm">Belum ada data distribusi.</div>
        @endif

        @if($popularDestinations->isNotEmpty() && $popularDestinations->first()->destination)
        <div class="mt-10 p-6 bg-gradient-to-br from-emerald-700 to-emerald-800 rounded-2xl text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[11px] font-bold text-emerald-200 uppercase tracking-[0.2em] mb-2">Destinasi Paling Sering Direkomendasikan</p>
                <h4 class="text-xl font-bold mb-2">{{ $popularDestinations->first()->destination->name }}</h4>
                @if($popularDestinations->first()->destination->category)
                <p class="text-emerald-300 text-xs mb-3">{{ ucwords(str_replace('_', ' ', $popularDestinations->first()->destination->category)) }}</p>
                @endif
                <div class="flex items-center gap-4 mt-4">
                    <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl">
                        <p class="text-[10px] text-emerald-200 uppercase font-bold tracking-wider">Total Rekomendasi</p>
                        <p class="text-lg font-bold">{{ number_format($popularDestinations->first()->count) }} Kali</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl">
                        <p class="text-[10px] text-emerald-200 uppercase font-bold tracking-wider">Click Rate</p>
                        <p class="text-lg font-bold">{{ $clickRate }}%</p>
                    </div>
                </div>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-40 h-40 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        </div>
        @endif
    </div>

    <!-- User Preferences -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Preferensi Populer</h3>
        <p class="text-sm text-gray-400 mb-8 font-medium">
            Kategori destinasi yang paling banyak dipilih pengguna
        </p>

        <div class="space-y-6 flex-grow">
            @forelse($userPreferences as $preference => $percent)
                <div class="flex items-center gap-4">
                    <div class="flex-grow">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[14px] font-bold text-gray-700">{{ $preference }}</span>
                            <span class="text-[13px] font-bold text-gray-500">{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-50 rounded-full h-2 border border-gray-100 overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-gray-400 text-sm">
                    Belum ada data preferensi.<br>
                    <span class="text-xs text-gray-300">Data akan muncul saat behavior_data terisi dari Go backend.</span>
                </div>
            @endforelse
        </div>

        <div class="mt-8 pt-8 border-t border-gray-50 flex items-center gap-3">
            <a href="{{ route('admin.recommendations.export') }}"
               class="flex-grow flex items-center justify-center gap-3 px-6 py-4 bg-emerald-700 text-white rounded-2xl font-bold text-sm hover:bg-emerald-800 transition-all shadow-lg shadow-emerald-700/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>
        </div>
    </div>
</div>

<!-- History Table -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-white border-b border-gray-50">
                    <th class="px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Trip ID</th>
                    <th class="px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                    <th class="px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Destinasi</th>
                    <th class="px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Durasi</th>
                    <th class="px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Preferensi</th>
                    <th class="px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Tgl Dibuat</th>
                    <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($logs as $index => $log)
                    @php
                        $isRegistered = $log->user
                            && !empty($log->user->password)
                            && (!empty($log->user->email) || !empty($log->user->name));

                        $userName = $log->behavior_data['user_name']
                            ?? ($isRegistered ? ($log->user->name ?? 'User Terdaftar') : 'Tamu');

                        // Categories from behavior_data
                        $cats = [];
                        if (!empty($log->behavior_data['categories']) && is_array($log->behavior_data['categories'])) {
                            $catLabels = [
                                'wisata_alam'    => 'Alam',
                                'wisata_budaya'  => 'Budaya',
                                'wisata_kuliner' => 'Kuliner',
                                'wisata_religi'  => 'Religi',
                                'wisata_sejarah' => 'Sejarah',
                            ];
                            foreach ($log->behavior_data['categories'] as $c) {
                                $key  = strtolower(trim((string)$c));
                                $cats[] = $catLabels[$key] ?? ucwords(str_replace('_', ' ', $key));
                            }
                        } elseif ($log->destination?->category) {
                            // Fallback: derive from destination's category
                            $catLabels = [
                                'wisata_alam'    => 'Alam',
                                'wisata_budaya'  => 'Budaya',
                                'wisata_kuliner' => 'Kuliner',
                                'wisata_religi'  => 'Religi',
                                'wisata_sejarah' => 'Sejarah',
                            ];
                            $key  = strtolower($log->destination->category);
                            $cats[] = $catLabels[$key] ?? ucwords(str_replace('_', ' ', $key));
                        }
                    @endphp
                    <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                        <td class="px-10 py-6">
                            <span class="font-mono text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">
                                #TRP-{{ strtoupper(substr($log->_id, -6)) }}
                            </span>
                        </td>
                        <td class="px-10 py-6">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-gray-800">{{ $userName }}</span>
                                @if($isRegistered)
                                    <span class="inline-flex items-center w-max px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#E6F6F2] text-[#00A884] uppercase tracking-wide border border-[#00A884]/10">👤 User</span>
                                @else
                                    <span class="inline-flex items-center w-max px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wide border border-gray-100">👥 Guest</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <span class="text-sm font-semibold text-gray-700">
                                {{ $log->destination?->name ?? '<span class="text-gray-400 italic">N/A</span>' }}
                            </span>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">
                                {{ round($log->recommendation_score) }} Hari
                            </span>
                        </td>
                        <td class="px-10 py-6">
                            @if(!empty($cats))
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($cats, 0, 2) as $cat)
                                        <span class="px-2 py-0.5 bg-gray-50 text-gray-600 text-[10px] font-bold rounded-lg border border-gray-100">{{ $cat }}</span>
                                    @endforeach
                                    @if(count($cats) > 2)
                                        <span class="px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-bold rounded-lg border border-gray-100">+{{ count($cats) - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-300 text-xs italic">-</span>
                            @endif
                        </td>
                        <td class="px-10 py-6">
                            @if($log->is_clicked)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Diklik
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold border border-amber-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Diabaikan
                                </span>
                            @endif
                        </td>
                        <td class="px-10 py-6">
                            <div class="text-[13px] text-gray-600 font-medium">
                                {{ $log->created_at?->format('d M Y, H:i') ?? '-' }}
                            </div>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <a href="{{ route('admin.recommendations.show', $log->_id) }}"
                               class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all inline-flex" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-10 py-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                <p class="text-sm font-bold">Tidak ada data rekomendasi</p>
                                <p class="text-xs mt-1 text-gray-300">Data akan muncul saat pengguna menggunakan fitur SmartTrip</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($logs) && method_exists($logs, 'links') && $logs->total() > 0)
    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between bg-white">
        <div class="text-gray-400 text-sm font-medium">
            Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} Rekomendasi
        </div>
        <div>{{ $logs->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
    </div>
    @endif
</div>

@endsection
