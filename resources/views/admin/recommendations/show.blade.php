@extends('admin.layouts.app')

@section('title', 'Recommendation Detail')
@section('navbar_title', 'Detail Rencana Trip')
@section('page_title', 'Detail Rencana Trip')
@section('page_description', 'Informasi lengkap rekomendasi destinasi dan riwayat trip planner dari sistem AI SmartTrip')

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
    $tripId      = '#TRP-' . strtoupper(substr($log->_id, -6));
    $destName    = $log->destination?->name ?? 'N/A';
    $destCategory = $log->destination?->category ?? null;
    $duration    = $tripDuration ?? round($log->recommendation_score);

    $isRegistered = $log->user
        && !empty($log->user->password)
        && (!empty($log->user->email) || !empty($log->user->name));

    $userName = $log->behavior_data['user_name']
        ?? ($isRegistered ? ($log->user->name ?? 'User Terdaftar') : 'Guest / Tamu');

    $userEmail = $isRegistered ? ($log->user->email ?? null) : null;

    // Budget from behavior_data
    $budgetRaw = $budget ?? ($log->behavior_data['budget'] ?? null);

    $catLabels = [
        'wisata_alam'    => 'Wisata Alam',
        'wisata_budaya'  => 'Wisata Budaya',
        'wisata_kuliner' => 'Wisata Kuliner',
        'wisata_religi'  => 'Wisata Religi',
        'wisata_sejarah' => 'Wisata Sejarah',
    ];
    $destCategoryLabel = $destCategory ? ($catLabels[strtolower($destCategory)] ?? ucwords(str_replace('_', ' ', $destCategory))) : null;
@endphp

<!-- Header -->
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
            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[12px] font-bold rounded-xl uppercase tracking-wider">
                {{ $duration }} Hari
            </span>
        </div>

        @if($budgetRaw)
        <div class="h-12 w-px bg-gray-100 hidden md:block"></div>
        <div>
            <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Budget</p>
            <span class="text-[15px] font-bold text-gray-800">Rp {{ number_format((int)$budgetRaw, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="h-12 w-px bg-gray-100 hidden md:block"></div>

        <div>
            <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Dibuat Tanggal</p>
            <p class="text-[15px] font-bold text-gray-800">
                {{ $log->created_at?->format('d M Y, H:i') ?? '-' }} WIB
            </p>
        </div>

        <div class="flex items-center gap-3 ml-auto">
            @if($log->is_clicked)
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-[13px] font-bold border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Diklik Pengguna
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-600 rounded-xl text-[13px] font-bold border border-amber-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Ditampilkan, Belum Diklik
                </span>
            @endif
            <a href="{{ route('admin.recommendations.index') }}"
               class="px-6 py-3 bg-gray-50 text-gray-500 rounded-xl text-[14px] font-bold hover:bg-gray-100 transition-all border border-transparent hover:border-gray-200">
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Destination Hero -->
        <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="h-64 bg-emerald-700 relative overflow-hidden">
                @if($log->destination && !empty($log->destination->images[0]))
                    <img src="{{ $log->destination->images[0] }}"
                         alt="{{ $destName }}"
                         class="absolute inset-0 w-full h-full object-cover opacity-60">
                @else
                    <div class="absolute inset-0 opacity-10"
                         style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220%200%2060%2060%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.4%22%3E%3Cpath d=%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                @endif
                <div class="absolute bottom-8 left-8">
                    <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md text-white text-[11px] font-bold rounded-xl uppercase tracking-wider mb-3 inline-block">Destinasi Utama</span>
                    <h2 class="text-3xl font-bold text-white">{{ $destName }}</h2>
                    @if($destCategoryLabel)
                        <p class="text-emerald-200 text-sm mt-1">{{ $destCategoryLabel }}</p>
                    @endif
                </div>
            </div>
            <div class="p-8">
                @if($log->destination?->description)
                    <p class="text-gray-600 leading-relaxed text-[15px]">{{ $log->destination->description }}</p>
                @else
                    <p class="text-gray-400 leading-relaxed text-[15px] italic">Deskripsi destinasi tidak tersedia.</p>
                @endif

                <!-- Real destination stats -->
                @if($destinationStats)
                <div class="mt-8 grid grid-cols-2 gap-6 p-6 bg-gray-50/50 rounded-2xl border border-gray-100">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rating</p>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-amber-500">{{ number_format($destinationStats['avg_rating'], 1) }}</span>
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($destinationStats['avg_rating']))
                                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Total Ulasan</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ number_format($destinationStats['total_reviews']) }}
                            <span class="text-xs text-gray-400 font-medium ml-1">Ulasan</span>
                        </p>
                    </div>
                </div>
                @endif

                @if($log->destination?->location)
                <div class="mt-4 flex items-center gap-2 text-gray-500 text-sm">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                    <span>{{ $log->destination->location }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Itinerary / Trip Plans — tampilkan satu card saja --}}
        @php
            $hasItinerary  = !empty($itinerary);
            $hasTripPlans  = isset($tripPlans) && $tripPlans->isNotEmpty();
        @endphp

        @if($hasItinerary)
        {{-- Ada itinerary di behavior_data --}}
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-gray-900">Rencana Perjalanan Terperinci</h3>
                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-xl text-[11px] font-bold">{{ $duration }} Hari</span>
            </div>
            <p class="text-sm text-gray-400 mb-8">Detail itinerary harian dari sistem AI SmartTrip</p>
            <div class="space-y-10">
                @foreach($itinerary as $dayIdx => $day)
                @php
                    $dayNum        = $day['day']        ?? ($dayIdx + 1);
                    $dayTitle      = $day['title']       ?? "Hari Ke-{$dayNum}";
                    $dayDesc       = $day['description'] ?? null;
                    $dayActivities = $day['activities']  ?? [];
                    $isLast        = $loop->last;
                @endphp
                <div class="flex gap-6 relative">
                    @if(!$isLast)
                        <div class="absolute left-6 top-14 bottom-[-40px] w-0.5 bg-emerald-50"></div>
                    @endif
                    <div class="relative z-10 w-12 h-12 bg-emerald-700 rounded-2xl flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-700/20 shrink-0">
                        {{ $dayNum }}
                    </div>
                    <div class="flex-grow pt-1">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $dayTitle }}</h4>
                        @if($dayDesc)
                            <p class="text-gray-500 text-[14px] leading-relaxed mb-4">{{ $dayDesc }}</p>
                        @endif
                        @if(!empty($dayActivities))
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($dayActivities as $act)
                            <div class="flex items-start gap-3 px-4 py-3 bg-gray-50 rounded-xl border border-gray-100/50">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></div>
                                <span class="text-[13px] text-gray-600 font-medium">
                                    {{ is_array($act) ? ($act['name'] ?? $act['activity'] ?? json_encode($act)) : $act }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @elseif($hasTripPlans)
        {{-- Tidak ada itinerary di behavior_data, tapi ada trip_plans dari SmartTrip --}}
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-gray-900">Trip Plans SmartTrip</h3>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-xl text-[11px] font-bold uppercase tracking-wider">
                    {{ $tripPlans->count() }} Plan
                </span>
            </div>
            <p class="text-sm text-gray-400 mb-6">Rencana perjalanan yang dibuat pengguna ini di SmartTrip</p>

            <div class="space-y-4">
                @foreach($tripPlans as $plan)
                @php
                    $summary      = $plan->summary ?? [];
                    $planTitle    = $summary['title']              ?? 'Trip Plan';
                    $planDays     = (int) ($summary['total_days']  ?? 0);
                    $planDests    = (int) ($summary['total_destinations'] ?? 0);
                    $planStart    = $summary['start_location']     ?? null;
                    $planTransport= $summary['transport']          ?? null;
                    $summaryDays  = $summary['days']               ?? [];
                @endphp
                <div class="border border-gray-100 rounded-2xl overflow-hidden">
                    {{-- Header --}}
                    <div class="p-5 bg-gray-50/60">
                        <p class="text-[14px] font-bold text-gray-800 mb-2">{{ $planTitle }}</p>
                        <div class="flex flex-wrap items-center gap-2">
                            @if($planDays)
                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 text-[11px] font-bold rounded-lg">{{ $planDays }} Hari</span>
                            @endif
                            @if($planDests)
                                <span class="px-2.5 py-0.5 bg-purple-50 text-purple-600 text-[11px] font-bold rounded-lg">{{ $planDests }} Destinasi</span>
                            @endif
                            @if($planStart)
                                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-500 text-[11px] font-bold rounded-lg">📍 {{ $planStart }}</span>
                            @endif
                            @if($planTransport)
                                <span class="px-2.5 py-0.5 bg-amber-50 text-amber-600 text-[11px] font-bold rounded-lg">🚗 {{ $planTransport }}</span>
                            @endif
                            <span class="ml-auto text-[11px] text-gray-400">{{ $plan->created_at?->format('d M Y') ?? '-' }}</span>
                        </div>
                    </div>
                    {{-- Days ringkas --}}
                    @if(!empty($summaryDays))
                    <div class="px-5 py-4 space-y-4">
                        @foreach($summaryDays as $dayIndex => $day)
                        @php
                            $dNum        = $day['day_number']  ?? ($dayIndex + 1);
                            $dLabel      = $day['date_label']  ?? "Hari Ke-{$dNum}";
                            $dStart      = $day['start_from']  ?? null;
                            $dSmartTip   = $day['smart_tip']   ?? null;
                            $dActivities = $day['activities']  ?? [];
                        @endphp
                        <div class="flex items-start gap-3">
                            <span class="w-7 h-7 bg-emerald-700 text-white text-[10px] font-bold rounded-xl flex items-center justify-center shrink-0 mt-0.5">{{ $dNum }}</span>
                            <div class="flex-grow">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-[13px] font-bold text-gray-700">{{ $dLabel }}</p>
                                    @if($dStart)
                                        <span class="text-[10px] text-gray-400">dari {{ $dStart }}</span>
                                    @endif
                                </div>
                                {{-- Aktivitas --}}
                                @if(!empty($dActivities))
                                <div class="space-y-1.5">
                                    @foreach($dActivities as $act)
                                    @php
                                        $actTime      = $act['time'] ?? null;
                                        $actName      = $act['name'] ?? '-';
                                        $actMode      = $act['travel_mode'] ?? null;
                                        $actDuration  = $act['duration_hours'] ?? null;
                                        $actMeta      = trim(($actMode ?? '') . ($actMode && $actDuration ? ' · ' : '') . ($actDuration ? $actDuration . ' jam' : ''));
                                    @endphp
                                    <div class="flex items-start gap-2 px-3 py-2 bg-gray-50 rounded-xl">
                                        @if($actTime)
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded shrink-0 mt-0.5">{{ $actTime }}</span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-bold text-gray-700 truncate">{{ $actName }}</p>
                                            @if($actMeta)
                                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $actMeta }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                {{-- Smart tip --}}
                                @if($dSmartTip)
                                <div class="mt-2 flex items-start gap-2 px-3 py-2 bg-amber-50 rounded-xl border border-amber-100">
                                    <span class="text-amber-500 shrink-0 text-[12px]">💡</span>
                                    <p class="text-[11px] text-amber-700 leading-relaxed">{{ $dSmartTip }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        @endif
        {{-- Jika tidak ada itinerary DAN tidak ada trip_plans: tidak tampilkan card sama sekali --}}
    </div>

    <!-- Sidebar -->
    <div class="space-y-8">

        <!-- User Context -->
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Konteks Pengguna</h4>

            <div class="space-y-6">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tipe Pengguna</p>
                    @if($isRegistered)
                        <span class="inline-flex items-center px-3 py-1 bg-[#E6F6F2] text-[#00A884] text-[11px] font-bold rounded-lg uppercase tracking-wider border border-[#00A884]/10">
                            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                            User Terdaftar
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-gray-50 text-gray-500 text-[11px] font-bold rounded-lg uppercase tracking-wider border border-gray-100">
                            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h12v-1a7 7 0 00-7-7z"></path></svg>
                            Guest / Tamu
                        </span>
                    @endif
                </div>

                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Pengguna</p>
                    <p class="text-[15px] font-bold text-gray-800">{{ $userName }}</p>
                    @if($userEmail)
                        <p class="text-xs text-gray-400 mt-1">{{ $userEmail }}</p>
                    @endif
                </div>

                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">User ID</p>
                    <p class="text-[12px] font-mono text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg break-all border border-emerald-100/50">
                        {{ $log->user_id ?? 'ANONYMOUS_SESSION' }}
                    </p>
                </div>

                <!-- Real preferences -->
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Preferensi Kategori</p>
                    @if(!empty($preferences))
                        <div class="flex flex-wrap gap-2">
                            @foreach($preferences as $pref)
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-lg border border-emerald-100">{{ $pref }}</span>
                            @endforeach
                        </div>
                    @elseif($destCategoryLabel)
                        {{-- Fallback: use destination's category --}}
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-gray-50 text-gray-600 text-[11px] font-bold rounded-lg border border-gray-100">{{ $destCategoryLabel }}</span>
                        </div>
                        <p class="text-[10px] text-gray-300 mt-1">*Dari kategori destinasi</p>
                    @else
                        <p class="text-xs text-gray-300 italic">Tidak ada data preferensi tersimpan.</p>
                    @endif
                </div>

                @if($budgetRaw)
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Budget Perjalanan</p>
                    <p class="text-[15px] font-bold text-gray-800">Rp {{ number_format((int)$budgetRaw, 0, ',', '.') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- System Analysis -->
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Analisis Sistem</h4>

            <div class="space-y-6">
                <!-- Duration display — nilai langsung dari MongoDB -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Durasi Trip</p>
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[13px] font-bold">{{ $duration }} Hari</span>
                    </div>
                    <p class="text-[10px] text-gray-300">Dari field <span class="font-mono">recommendation_score</span> di MongoDB</p>
                </div>

                <!-- Recommendation score raw -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Skor Rekomendasi (Raw)</p>
                        <p class="text-[13px] font-bold text-blue-600">{{ round($log->recommendation_score, 1) }}</p>
                    </div>
                    <p class="text-[10px] text-gray-300">Nilai mentah dari MongoDB</p>
                </div>

                <!-- Destination activity status -->
                @if($log->destination)
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status Destinasi</p>
                    @if($log->destination->is_active ?? true)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 text-gray-400 rounded-lg text-xs font-bold border border-gray-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 inline-block"></span> Tidak Aktif
                        </span>
                    @endif
                </div>
                @endif

                <!-- Click status -->
                <div class="pt-4 border-t border-gray-50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Status Konversi</p>
                    @if($log->is_clicked)
                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-[13px] font-bold text-emerald-800">Diklik Pengguna</span>
                            </div>
                            <p class="text-[11px] text-emerald-600 leading-relaxed">
                                Pengguna membuka rekomendasi ini di aplikasi — artinya AI berhasil menarik minat pengguna terhadap destinasi ini.
                            </p>
                        </div>
                    @else
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-amber-400 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <span class="text-[13px] font-bold text-amber-800">Ditampilkan, Belum Diklik</span>
                            </div>
                            <p class="text-[11px] text-amber-700 leading-relaxed">
                                Rekomendasi sudah ditampilkan ke pengguna, namun belum ada interaksi. Bisa jadi kurang relevan atau pengguna belum meneruskan ke langkah berikutnya.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Raw Behavior Data (visible jika ada) -->
        @if(!empty($log->behavior_data))
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Behavior Data</h4>
            <div class="space-y-3">
                @foreach($log->behavior_data as $key => $value)
                    @if(!in_array($key, ['user_name', 'itinerary']))
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wide shrink-0 pt-0.5">
                            {{ ucwords(str_replace('_', ' ', $key)) }}
                        </span>
                        <span class="text-[12px] font-mono text-gray-700 text-right">
                            @if(is_array($value))
                                {{ implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : (string)$v, $value)) }}
                            @else
                                {{ is_bool($value) ? ($value ? 'Ya' : 'Tidak') : $value }}
                            @endif
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- MongoDB doc ID -->
        <div class="bg-gray-50 rounded-[20px] border border-gray-100 p-6">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">MongoDB Document ID</p>
            <p class="text-[11px] font-mono text-gray-500 break-all">{{ $log->_id }}</p>
        </div>
    </div>
</div>

@endsection
