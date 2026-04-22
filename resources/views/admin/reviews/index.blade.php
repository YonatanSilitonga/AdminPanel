@extends('admin.layouts.app')

@section('title', 'Ulasan Pengguna')
@section('navbar_title', 'Ulasan')
@section('page_title', 'Ulasan Pengguna')
@section('page_description', 'Moderasi dan analisis ulasan pengguna')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Ulasan & Laporan</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Ringkasan Ulasan</span>
</nav>
@endsection

@section('content')
<div x-data="{
    activeTab: 'summary',
    showViewModal: false,
    viewingReview: null,
    loading: false,

    async openViewModal(id) {
        this.loading = true;
        this.showViewModal = true;
        this.viewingReview = null;
        try {
            const res = await fetch(`/admin/reviews/${id}`, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            
            if (!res.ok) {
                let errorMsg = `HTTP ${res.status}: ${res.statusText}`;
                try {
                    const errorData = await res.json();
                    if (errorData.error) {
                        errorMsg = errorData.error;
                    } else if (errorData.message) {
                        errorMsg = errorData.message;
                    }
                } catch (e) {
                    // response is not JSON, use status text
                }
                throw new Error(errorMsg);
            }
            
            const data = await res.json();
            console.log('Review data loaded:', data);
            this.viewingReview = data;
        } catch(e) {
            console.error('Error loading review:', e);
            alert('❌ Gagal mengambil data ulasan:\n' + e.message);
            this.showViewModal = false;
        } finally {
            this.loading = false;
        }
    },

    stars(n) {
        return '★'.repeat(n) + '☆'.repeat(5 - n);
    }
}">
    @php
        $sentimentSummary = $sentimentSummary ?? ['total' => 0, 'positive' => 0, 'neutral' => 0, 'negative' => 0, 'pending' => 0];
        $ratingDistribution = $ratingDistribution ?? [];
        $keywordSummary = $keywordSummary ?? [
            'overall' => [
                'review_count' => 0,
                'sentiment_counts' => ['negative' => 0, 'neutral' => 0, 'positive' => 0],
                'top_keywords' => [],
                'top_keywords_by_sentiment' => ['negative' => [], 'neutral' => [], 'positive' => []],
            ],
            'destinations' => [],
        ];
        $predictionSummary = $predictionSummary ?? [];
        $keywordModelVersion = $keywordModelVersion ?? null;

        // Build weighted cloud style from Python keyword payload.
        $buildKeywordCloud = function (array $items): array {
            $normalized = [];

            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $keyword = trim((string) ($item['keyword'] ?? ''));
                if ($keyword === '') {
                    continue;
                }

                $count = (int) ($item['count'] ?? 0);
                if ($count < 0) {
                    $count = 0;
                }

                $normalized[] = [
                    'keyword' => $keyword,
                    'count' => $count,
                ];
            }

            if (empty($normalized)) {
                return [];
            }

            $counts = array_column($normalized, 'count');
            $minCount = min($counts);
            $maxCount = max($counts);
            $range = $maxCount - $minCount;

            return array_map(function (array $item) use ($minCount, $range) {
                $norm = $range > 0 ? (($item['count'] - $minCount) / $range) : 1.0;

                // Higher count = bigger, bolder, and darker.
                $sizePx = 12 + ($norm * 8);
                $weight = 500 + ($norm * 200);
                $lightness = 56 - ($norm * 16);

                $item['style'] = sprintf(
                    'font-size: %.1fpx; font-weight: %.0f; color: hsl(214 26%% %.1f%%);',
                    $sizePx,
                    $weight,
                    $lightness
                );

                $item['norm'] = $norm;

                return $item;
            }, $normalized);
        };
    @endphp

    <div class="mb-8 border-b border-gray-200">
        <nav class="flex gap-8 -mb-px">
            <button type="button"
                @click="activeTab = 'summary'"
                :class="activeTab === 'summary' ? 'border-sidebar text-sidebar' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="pb-4 border-b-2 text-sm font-bold transition-colors">
                Ringkasan Ulasan
            </button>
            <button type="button"
                @click="activeTab = 'list'"
                :class="activeTab === 'list' ? 'border-sidebar text-sidebar' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="pb-4 border-b-2 text-sm font-bold transition-colors">
                Daftar Ulasan
            </button>
        </nav>
    </div>

    <div x-show="activeTab === 'summary'" class="space-y-8">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[2rem] bg-white border border-gray-100 shadow-sm p-5">
                <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Total Ulasan</p>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ number_format($sentimentSummary['total']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-emerald-50 border border-emerald-100 shadow-sm p-5">
                <p class="text-xs uppercase tracking-widest text-emerald-500 font-bold">Ulasan Positif</p>
                <p class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($sentimentSummary['positive']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-amber-50 border border-amber-100 shadow-sm p-5">
                <p class="text-xs uppercase tracking-widest text-amber-500 font-bold">Ulasan Netral</p>
                <p class="mt-3 text-3xl font-black text-amber-700">{{ number_format($sentimentSummary['neutral']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-red-50 border border-red-100 shadow-sm p-5">
                <p class="text-xs uppercase tracking-widest text-red-500 font-bold">Ulasan Negatif</p>
                <p class="mt-3 text-3xl font-black text-red-700">{{ number_format($sentimentSummary['negative']) }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Distribusi Rating</h3>
                <div class="space-y-4">
                    @foreach([5,4,3,2,1] as $rating)
                        @php
                            $count = $ratingDistribution[$rating]['count'] ?? 0;
                            $percentage = $ratingDistribution[$rating]['percentage'] ?? 0;
                        @endphp
                        <div class="flex items-center gap-4">
                            <span class="w-4 text-sm font-semibold text-gray-600">{{ $rating }}</span>
                            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-sidebar rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="w-12 text-right text-sm text-gray-500">{{ $percentage }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Ringkasan Cepat</h3>
                <div class="space-y-4 text-sm text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Pending sentiment</span>
                        <span class="font-bold text-gray-900">{{ number_format($sentimentSummary['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Persentase positif</span>
                        <span class="font-bold text-emerald-700">
                            {{ $sentimentSummary['total'] > 0 ? round(($sentimentSummary['positive'] / $sentimentSummary['total']) * 100) : 0 }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Persentase negatif</span>
                        <span class="font-bold text-red-600">
                            {{ $sentimentSummary['total'] > 0 ? round(($sentimentSummary['negative'] / $sentimentSummary['total']) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
                <p class="mt-6 text-xs text-gray-400 leading-relaxed">
                    Ringkasan ini hanya menampilkan statistik umum. Analisis sentimen detail tetap tersedia pada tab daftar ulasan saat proses analisis dijalankan.
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Keyword Populer</h3>
                    @if(!empty($keywordModelVersion))
                        <span class="text-[11px] font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-600">Model: {{ $keywordModelVersion }}</span>
                    @endif
                </div>
                @php
                    $overallKeywords = $keywordSummary['overall']['top_keywords'] ?? [];
                    $overallCounts = $keywordSummary['overall']['sentiment_counts'] ?? ['negative' => 0, 'neutral' => 0, 'positive' => 0];
                    $overallCloud = array_slice($buildKeywordCloud($overallKeywords), 0, 12);
                @endphp
                @if(!empty($overallCloud))
                    <div class="flex items-center gap-3 mb-4 text-xs text-gray-500">
                        <span>Positif: <strong class="text-emerald-700">{{ $overallCounts['positive'] ?? 0 }}</strong></span>
                        <span>Netral: <strong class="text-amber-700">{{ $overallCounts['neutral'] ?? 0 }}</strong></span>
                        <span>Negatif: <strong class="text-red-700">{{ $overallCounts['negative'] ?? 0 }}</strong></span>
                    </div>
                    <div class="flex flex-wrap items-end gap-x-3 gap-y-2">
                        @foreach($overallCloud as $word)
                            <span class="inline-flex items-end gap-1.5 px-2 py-1 " style="{{ $word['style'] }}">
                                <span>{{ $word['keyword'] }}</span>
                                <span class="text-[11px] font-semibold text-gray-400">{{ $word['count'] }}</span>
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Belum ada keyword populer.</p>
                @endif
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Keyword Per Sentimen</h3>
                @php
                    $bySentiment = $keywordSummary['overall']['top_keywords_by_sentiment'] ?? [];
                    $groups = [
                        'positive' => ['title' => 'Positif', 'class' => 'text-emerald-700 bg-emerald-50 border-emerald-100'],
                        'neutral' => ['title' => 'Netral', 'class' => 'text-amber-700 bg-amber-50 border-amber-100'],
                        'negative' => ['title' => 'Negatif', 'class' => 'text-red-700 bg-red-50 border-red-100'],
                    ];
                @endphp
                <div class="space-y-4">
                    @foreach($groups as $key => $meta)
                        @php
                            $items = $bySentiment[$key] ?? [];
                            $cloud = array_slice($buildKeywordCloud($items), 0, 6);
                        @endphp
                        <div class="border rounded-xl p-3 {{ $meta['class'] }}">
                            <p class="text-xs font-bold uppercase tracking-wider mb-2">{{ $meta['title'] }}</p>
                            @if(!empty($cloud))
                                <div class="flex flex-wrap items-end gap-x-2.5 gap-y-2">
                                    @foreach($cloud as $word)
                                        <span class="inline-flex items-end gap-1 px-2 py-1 rounded" style="{{ $word['style'] }}">
                                            <span>{{ $word['keyword'] }}</span>
                                            <span class="text-[10px] font-semibold text-gray-400">{{ $word['count'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs opacity-75">Belum ada data.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Keyword Per Destinasi</h3>
            @if(!empty($keywordSummary['destinations']))
                <div class="space-y-3">
                    @foreach(array_slice($keywordSummary['destinations'], 0, 5) as $destination)
                        @php
                            $destinationName = $destination['destination_name'] ?? $destination['name'] ?? $destination['destination_id'] ?? 'Destinasi';
                            $destinationKeywords = $destination['top_keywords'] ?? [];
                            $destinationCloud = array_slice($buildKeywordCloud($destinationKeywords), 0, 6);
                            $destinationSentimentCounts = $destination['sentiment_counts'] ?? ['negative' => 0, 'neutral' => 0, 'positive' => 0];
                            $reviewCount = $destination['review_count'] ?? null;
                        @endphp
                        <div class="border border-gray-100 rounded-xl p-4">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-sm font-bold text-gray-800">{{ $destinationName }}</p>
                                @if($reviewCount !== null)
                                    <span class="text-xs text-gray-500">{{ $reviewCount }} ulasan</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mb-3 text-[11px] text-gray-500">
                                <span>Positif: <strong class="text-emerald-700">{{ $destinationSentimentCounts['positive'] ?? 0 }}</strong></span>
                                <span>Netral: <strong class="text-amber-700">{{ $destinationSentimentCounts['neutral'] ?? 0 }}</strong></span>
                                <span>Negatif: <strong class="text-red-700">{{ $destinationSentimentCounts['negative'] ?? 0 }}</strong></span>
                            </div>
                            @if(!empty($destinationCloud))
                                <div class="flex flex-wrap items-end gap-x-2.5 gap-y-2">
                                    @foreach($destinationCloud as $word)
                                        <span class="inline-flex items-end gap-1 px-2 py-1" style="{{ $word['style'] }}">
                                            <span>{{ $word['keyword'] }}</span>
                                            <span class="text-[10px] font-semibold text-gray-400">{{ $word['count'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400">Belum ada keyword untuk destinasi ini.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Data keyword per destinasi belum tersedia.</p>
            @endif
        </div>
    </div>

    <div x-show="activeTab === 'list'" class="space-y-8">
        {{-- Filter & Search --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap items-center gap-4">
                <div class="relative w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teks ulasan..."
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-300">
                </div>
                <select name="rating" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium">
                    <option value="">Semua Rating</option>
                    @foreach([5,4,3,2,1] as $r)
                        <option value="{{ $r }}" @selected(request('rating') == $r)>{{ $r }} Bintang</option>
                    @endforeach
                </select>
            </form>

            <form method="POST" action="{{ route('admin.reviews.analyze-batch') }}">
                @csrf
                <input type="hidden" name="limit" value="50">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-sidebar text-white text-sm font-bold shadow-sm hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Analisis Pending
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Destinasi</th>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Ulasan</th>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Sentimen</th>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Confidence</th>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse(($reviews ?? []) as $review)
                            <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-sidebar/10 rounded-full flex items-center justify-center text-sidebar text-xs font-bold">
                                            {{ strtoupper(substr($review->user_id ?? 'A', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-700">{{ $review->user_id ?? 'Anonim' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <span class="text-sm text-gray-500 font-medium">{{ optional($review->destination)->name ?? 'Umum' }}</span>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-1">
                                        @for($i = 0; $i < ($review->rating ?? 0); $i++)
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @endfor
                                        @for($i = ($review->rating ?? 0); $i < 5; $i++)
                                            <svg class="w-4 h-4 text-gray-200 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-10 py-6 max-w-xs">
                                    <p class="text-sm text-gray-500 truncate">{{ $review->review ?? '-' }}</p>
                                </td>
                                <td class="px-10 py-6">
                                    <span @class([
                                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold',
                                        'bg-emerald-50 text-emerald-600' => $review->sentiment_label === 'positive',
                                        'bg-amber-50 text-amber-600' => $review->sentiment_label === 'neutral',
                                        'bg-red-50 text-red-600' => $review->sentiment_label === 'negative',
                                        'bg-gray-100 text-gray-500' => empty($review->sentiment_label),
                                    ])>
                                        {{ $review->sentiment_label ? ucfirst($review->sentiment_label) : 'Pending' }}
                                    </span>
                                    @if(!empty($review->sentiment_reason))
                                        <p class="mt-2 text-[11px] text-gray-400 max-w-[11rem] truncate">{{ $review->sentiment_reason }}</p>
                                    @endif
                                </td>
                                <td class="px-10 py-6">
                                    <span class="text-sm font-semibold text-gray-600">
                                        {{ isset($review->sentiment_confidence) ? number_format((float) $review->sentiment_confidence, 2) : '-' }}
                                    </span>
                                </td>
                                <td class="px-10 py-6">
                                    <span class="text-xs text-gray-400 font-medium">{{ $review->created_at?->diffForHumans() ?? '-' }}</span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <form method="POST" action="{{ route('admin.reviews.analyze', $review->_id) }}">
                                            @csrf
                                            <button type="submit" class="p-2.5 bg-emerald-50 text-emerald-600 rounded-full hover:bg-emerald-100 transition-all" title="Analisis sentiment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            </button>
                                        </form>
                                        <button @click="openViewModal('{{ $review->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.reviews.destroy', $review->_id) }}', title: 'Hapus Ulasan', type: 'ulasan', name: {{ json_encode('dari ' . ($review->user_id ?? 'Anonim')) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-8 py-14 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                        <p class="text-sm font-medium">Tidak ada ulasan ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($reviews) && method_exists($reviews, 'links'))
        <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
            <div class="text-gray-400 text-sm font-medium">Menampilkan {{ $reviews->count() }} dari {{ $reviews->total() }} Ulasan</div>
            <div>{{ $reviews->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
        </div>
        @endif
    </div>

    {{-- VIEW REVIEW MODAL --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/20 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Detail Ulasan</h3>
                    <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !viewingReview" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>

                <div x-show="viewingReview" class="space-y-5">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                        <div class="w-12 h-12 bg-sidebar/10 rounded-full flex items-center justify-center text-sidebar font-bold text-lg">
                            <span x-text="viewingReview ? viewingReview.user_id?.charAt(0)?.toUpperCase() || 'A' : 'A'"></span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800" x-text="viewingReview?.user_id || 'Anonim'"></p>
                            <p class="text-xs text-gray-400" x-text="viewingReview?.created_at ? new Date(viewingReview.created_at).toLocaleDateString('id-ID', {year:'numeric', month:'long', day:'numeric'}) : ''"></p>
                        </div>
                        <div class="ml-auto text-2xl text-yellow-400" x-text="stars(viewingReview?.rating || 0)"></div>
                    </div>

                    <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                        <label class="text-xs font-bold text-blue-500 uppercase tracking-widest">Destinasi</label>
                        <p class="mt-2 text-sm font-bold text-blue-900" x-text="viewingReview?.destination?.name || 'Tidak diketahui'"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-4 bg-gray-50 rounded-2xl">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sentimen</label>
                            <p class="mt-2 text-sm font-bold text-gray-800" x-text="viewingReview?.sentiment_label ? viewingReview.sentiment_label.charAt(0).toUpperCase() + viewingReview.sentiment_label.slice(1) : 'Pending'"></p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Confidence</label>
                            <p class="mt-2 text-sm font-bold text-gray-800" x-text="viewingReview?.sentiment_confidence ? Number(viewingReview.sentiment_confidence).toFixed(2) : '-' "></p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Reason</label>
                            <p class="mt-2 text-sm font-bold text-gray-800 truncate" x-text="viewingReview?.sentiment_reason || '-' "></p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Ulasan</label>
                        <p class="text-sm text-gray-700 font-medium leading-relaxed p-4 bg-gray-50 rounded-2xl" x-text="viewingReview?.review || '-'"></p>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button @click="showViewModal = false" class="px-8 py-3 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
