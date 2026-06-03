@extends('admin.layouts.app')

@section('title', 'Ulasan Pengguna')
@section('navbar_title', 'Ulasan')
@section('page_title', 'Ulasan Pengguna')
@section('page_description', 'Moderasi dan analisis ulasan pengguna')

@section('page_actions')
<div class="flex items-center gap-4">
    <!-- Cetak Analitik (PDF) -->
    <div class="flex items-center gap-2">
        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-export-modal'))" class="flex items-center gap-2 px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-indigo-600/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Analitik (PDF)
        </button>
        <div class="relative group cursor-pointer inline-flex items-center">
            <svg class="w-4 h-4 text-gray-400 hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                <div class="space-y-2">
                    <div>
                        <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Cetak Analitik</span>
                        <p class="text-slate-200 font-sans leading-relaxed">Mencetak dokumen resmi PDF analitik sentimen ulasan lengkap dengan kop surat dinas, statistik distribusi rating, dan tanda tangan kepala dinas.</p>
                    </div>
                </div>
                <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
            </div>
        </div>
    </div>

    <!-- Ekspor Daftar Ulasan (CSV) -->
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.reviews.export', request()->query()) }}" class="flex items-center gap-2 px-8 py-3 bg-emerald-700 text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-emerald-700/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Ekspor Daftar Ulasan (CSV)
        </a>
        <div class="relative group cursor-pointer inline-flex items-center">
            <svg class="w-4 h-4 text-gray-400 hover:text-emerald-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
                <div class="space-y-2">
                    <div>
                        <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Ekspor Daftar Ulasan</span>
                        <p class="text-slate-200 font-sans leading-relaxed">Mengekspor data mentah seluruh ulasan yang disaring saat ini ke dalam berkas CSV untuk pengolahan data spreadsheet eksternal.</p>
                    </div>
                </div>
                <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
            </div>
        </div>
    </div>
</div>
@endsection

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
<div x-on:open-export-modal.window="showExportModal = true" x-data="{
    activeTab: (new URLSearchParams(window.location.search)).get('tab') || localStorage.getItem('active_review_tab') || 'summary',
    showViewModal: false,
    showExportModal: false,
    instansi: 'PEMERINTAH KABUPATEN TOBA/DINAS KEBUDAYAAN DAN PARIWISATA',
    alamat: 'Jl. Bukit Pagar Batu No. 1, Balige, Kabupaten Toba, Sumatera Utara',
    email: 'disbudpar@tobakab.go.id',
    telp: '(0632) 123456',
    website: 'https://disbudpar.tobakab.go.id',
    nomor_surat: '050/322/Disbudpar/{{ date('Y') }}',
    hal: 'Laporan Analitik Ulasan Pengguna',
    nama_penandatangan: 'Sandro M. S. Simanjuntak, S.T., M.Si.',
    nip_penandatangan: '19780512 200501 1 003',
    jabatan: 'Kepala Dinas Kebudayaan dan Pariwisata',

    init() {
        this.$watch('activeTab', value => {
            localStorage.setItem('active_review_tab', value);
            const url = new URL(window.location.href);
            url.searchParams.set('tab', value);
            window.history.replaceState({}, '', url.toString());

            if (value === 'summary') {
                this.$nextTick(() => {
                    setTimeout(() => {
                        if (typeof window.initReviewCharts === 'function') window.initReviewCharts();
                        if (typeof window.initReviewsWordCloud === 'function') window.initReviewsWordCloud();
                    }, 100);
                });
            }
        });

        if (this.activeTab === 'summary') {
            this.$nextTick(() => {
                setTimeout(() => {
                    if (typeof window.initReviewCharts === 'function') window.initReviewCharts();
                    if (typeof window.initReviewsWordCloud === 'function') window.initReviewsWordCloud();
                }, 200);
            });
        }
    },

    submitExport() {
        const form = this.$refs.exportForm;
        const params = new URLSearchParams(new FormData(form)).toString();
        window.open(`/admin/reviews/analytics/print?${params}`, '_blank');
        this.showExportModal = false;
    },

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
            window.showAlert('Gagal mengambil data ulasan: ' + e.message, 'Error', 'error');
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

        // Calculate average rating
        $totalRatingsCount = 0;
        $sumRatings = 0;
        foreach ([5, 4, 3, 2, 1] as $r) {
            $c = $ratingDistribution[$r]['count'] ?? 0;
            $totalRatingsCount += $c;
            $sumRatings += $r * $c;
        }
        $averageRating = $totalRatingsCount > 0 ? number_format($sumRatings / $totalRatingsCount, 1) : '0.0';

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
            <div class="rounded-[2rem] bg-white border border-gray-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Total Ulasan</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan akumulasi seluruh ulasan destinasi wisata dari pengguna.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Halaman monitoring ulasan and analitik sentimen.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ number_format($sentimentSummary['total']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-emerald-50 border border-emerald-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-emerald-500 font-bold">Ulasan Positif</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah ulasan yang dianalisis oleh model machine learning dan dikategorikan bersentimen positif.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Halaman ringkasan ulasan dan dashboard utama.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($sentimentSummary['positive']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-amber-50 border border-amber-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-amber-500 font-bold">Ulasan Netral</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-amber-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-amber-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah ulasan yang dianalisis oleh model machine learning dan dikategorikan bersentimen netral.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-amber-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Halaman ringkasan ulasan.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-black text-amber-700">{{ number_format($sentimentSummary['neutral']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-red-50 border border-red-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-red-500 font-bold">Ulasan Negatif</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-red-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan jumlah ulasan yang dianalisis oleh model machine learning dan dikategorikan bersentimen negatif.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Halaman ringkasan ulasan, dashboard, dan laporan keluhan.</p>
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-3xl font-black text-red-700">{{ number_format($sentimentSummary['negative']) }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Card 1: Tren Sentimen Ulasan (Line Chart) -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Tren Sentimen Ulasan</h3>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-600">
                        6 Bulan Terakhir
                    </span>
                </div>
                <div class="relative h-44 w-full">
                    <canvas id="sentimentTrendChart"></canvas>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400">
                    <div class="flex items-center gap-4 font-semibold">
                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span> Positif</div>
                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#f59e0b]"></span> Netral</div>
                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#ef4444]"></span> Negatif</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Distribusi Rating (Horizontal Bar Chart) -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Distribusi Rating</h3>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center text-yellow-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        </div>
                        <span class="text-base font-black text-gray-900">{{ $averageRating }} / 5.0</span>
                    </div>
                </div>
                <div class="relative h-44 w-full">
                    <canvas id="ratingBarChart"></canvas>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400">
                    <span>Total Ulasan Masuk</span>
                    <span class="font-bold text-gray-700">{{ number_format($totalRatingsCount) }} Ulasan</span>
                </div>
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
                    
                    // Build sentiment map for word cloud coloring
                    $sentimentMap = [];
                    foreach($keywordSummary['overall']['top_keywords_by_sentiment'] ?? [] as $sent => $kws) {
                        foreach($kws as $kw) {
                            $sentimentMap[$kw['keyword']] = $sent;
                        }
                    }
                @endphp
                @if(!empty($overallKeywords))
                    <div class="flex items-center gap-3 mb-4 text-xs text-gray-500">
                        <span>Positif: <strong class="text-emerald-700">{{ $overallCounts['positive'] ?? 0 }}</strong></span>
                        <span>Netral: <strong class="text-amber-700">{{ $overallCounts['neutral'] ?? 0 }}</strong></span>
                        <span>Negatif: <strong class="text-red-700">{{ $overallCounts['negative'] ?? 0 }}</strong></span>
                    </div>
                    <div class="relative w-full h-[350px] bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm flex items-center justify-center p-6">
                        <canvas id="word-cloud-canvas" class="w-full h-full cursor-default"></canvas>
                    </div>
                    <p class="mt-4 text-[11px] text-gray-400 leading-relaxed">
                        Ukuran kata melambangkan frekuensi kemunculan kata tersebut dalam seluruh ulasan yang dianalisis.
                    </p>
                @else
                    <p class="text-sm text-gray-500">Belum ada keyword populer.</p>
                @endif
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Keyword Per Sentimen</h3>
                @php
                    $bySentiment = $keywordSummary['overall']['top_keywords_by_sentiment'] ?? [];
                    $sentimentGroups = [
                        'positive' => [
                            'title' => 'Positif', 
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'color' => 'text-emerald-600',
                            'bg' => 'bg-emerald-50',
                            'bar' => 'bg-emerald-500'
                        ],
                        'neutral' => [
                            'title' => 'Netral', 
                            'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'color' => 'text-amber-600',
                            'bg' => 'bg-amber-50',
                            'bar' => 'bg-amber-500'
                        ],
                        'negative' => [
                            'title' => 'Negatif', 
                            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'color' => 'text-red-600',
                            'bg' => 'bg-red-50',
                            'bar' => 'bg-red-500'
                        ],
                    ];
                @endphp
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach($sentimentGroups as $key => $meta)
                        @php
                            $items = $bySentiment[$key] ?? [];
                            $maxCount = !empty($items) ? max(array_column($items, 'count')) : 1;
                        @endphp
                        <div class="rounded-2xl border border-gray-100 overflow-hidden flex flex-col">
                            <div class="px-4 py-3 {{ $meta['bg'] }} flex items-center gap-2 border-b border-gray-100">
                                <svg class="w-4 h-4 {{ $meta['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-wider {{ $meta['color'] }}">{{ $meta['title'] }}</span>
                            </div>
                            <div class="p-4 space-y-3 flex-1">
                                @forelse(array_slice($items, 0, 5) as $item)
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-[11px] font-medium">
                                            <span class="text-gray-700">{{ $item['keyword'] }}</span>
                                            <span class="text-gray-400">{{ $item['count'] }}</span>
                                        </div>
                                        <div class="h-1 w-full bg-gray-50 rounded-full overflow-hidden">
                                            <div class="h-full {{ $meta['bar'] }} opacity-60 rounded-full" style="width: {{ ($item['count'] / $maxCount) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-[11px] text-gray-400 italic text-center py-4">Belum ada data</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card: Analisis Sentimen Per Destinasi -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Analisis Sentimen Per Destinasi</h3>
                <span class="text-xs text-gray-400 font-medium">Berdasarkan 6 destinasi dengan ulasan terbanyak</span>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="destinationSentimentChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Keyword Per Destinasi</h3>
            @if(!empty($keywordSummary['destinations']))
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach(array_slice($keywordSummary['destinations'], 0, 6) as $destination)
                        @php
                            $destinationName = $destination['destination_name'] ?? $destination['name'] ?? $destination['destination_id'] ?? 'Destinasi';
                            $destinationKeywords = array_slice($destination['top_keywords'] ?? [], 0, 8);
                            $sentimentCounts = $destination['sentiment_counts'] ?? ['negative' => 0, 'neutral' => 0, 'positive' => 0];
                            $totalSent = array_sum($sentimentCounts);
                            $reviewCount = $destination['review_count'] ?? 0;
                        @endphp
                        <div class="group bg-gray-50/30 hover:bg-white border border-gray-100 hover:border-sidebar/20 rounded-2xl p-5 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-900 group-hover:text-sidebar transition-colors truncate">{{ $destinationName }}</h4>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $reviewCount }} ulasan teranalisis</p>
                                </div>
                                <div class="flex h-1.5 w-24 bg-gray-100 rounded-full overflow-hidden mt-2">
                                    @if($totalSent > 0)
                                        <div class="bg-emerald-500" style="width: {{ ($sentimentCounts['positive'] / $totalSent) * 100 }}%"></div>
                                        <div class="bg-amber-400" style="width: {{ ($sentimentCounts['neutral'] / $totalSent) * 100 }}%"></div>
                                        <div class="bg-red-500" style="width: {{ ($sentimentCounts['negative'] / $totalSent) * 100 }}%"></div>
                                    @else
                                        <div class="bg-gray-200 w-full"></div>
                                    @endif
                                </div>
                            </div>
                            
                            @if(!empty($destinationKeywords))
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($destinationKeywords as $kw)
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-white border border-gray-100 text-[11px] font-medium text-gray-600 shadow-sm">
                                            {{ $kw['keyword'] }}
                                            <span class="ml-1.5 text-[9px] text-gray-300 font-bold">{{ $kw['count'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-[11px] text-gray-400 italic">Belum ada keyword populer</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if(count($keywordSummary['destinations']) > 6)
                    <p class="mt-6 text-center text-xs text-gray-400 italic">Dan {{ count($keywordSummary['destinations']) - 6 }} destinasi lainnya...</p>
                @endif
            @else
                <div class="py-12 text-center">
                    <p class="text-sm text-gray-400">Data keyword per destinasi belum tersedia.</p>
                </div>
            @endif
        </div>
    </div>

    <div x-show="activeTab === 'list'" class="space-y-8">
        {{-- Filter & Search --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex-1">
                    <!-- Persist current sorting and tab -->
                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                    <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                    <input type="hidden" name="tab" value="list">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Cari Ulasan -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Cari Ulasan
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Mencari teks ulasan pengguna yang mengandung kata kunci tertentu.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                                <p class="text-slate-200 font-sans">Penyaringan baris data tabel ulasan.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teks ulasan..."
                                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-[#066466] outline-none text-[14px] font-medium placeholder-gray-400 transition-all shadow-sm">
                            </div>
                        </div>

                        <!-- Rating Bintang -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Rating Bintang
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Menyaring ulasan berdasarkan jumlah bintang rating (1 s/d 5) yang diberikan pengguna.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                                <p class="text-slate-200 font-sans">Analisis tingkat kepuasan ulasan pengguna.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </label>
                            <select name="rating" onchange="this.form.submit()"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                                <option value="">Semua Rating</option>
                                @foreach([5,4,3,2,1] as $r)
                                    <option value="{{ $r }}" @selected(request('rating') == $r)>{{ $r }} Bintang</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tampilkan -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Tampilkan
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Mengatur jumlah ulasan yang ditampilkan dalam satu halaman tabel.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                                <p class="text-slate-200 font-sans">Pagination halaman tabel ulasan.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </label>
                            <div class="flex items-center gap-2">
                                <select name="per_page" onchange="this.form.submit()"
                                    class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                                    @foreach([10, 15, 25, 50, 100] as $size)
                                        <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }} Baris</option>
                                    @endforeach
                                </select>
                                @if(request('search') || request('rating') || request('per_page') != 15)
                                    <a href="{{ route('admin.reviews.index', ['tab' => 'list']) }}" class="px-4 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Batch Analysis -->
                <div class="flex items-center gap-4 lg:mb-[2px] mt-4 lg:mt-0">
                    <form method="POST" action="{{ route('admin.reviews.analyze-batch') }}">
                        @csrf
                        <input type="hidden" name="limit" value="50">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#066466] hover:bg-[#055355] text-white text-sm font-bold shadow-sm transition-all whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Analisis Pending
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-white">
                        @php
                            $currentSort = request('sort_by', 'created_at');
                            $sortOrder = request('sort_order', 'desc') === 'asc' ? 'desc' : 'asc';
                        @endphp
                        <tr>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Destinasi</th>
                            <th class="px-10 py-6 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'rating', 'sort_order' => ($currentSort === 'rating' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Rating
                                    <svg class="w-4 h-4 {{ $currentSort === 'rating' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'rating' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                                                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Ulasan</th>
                            <th class="px-10 py-6 text-left">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'sentiment_label', 'sort_order' => ($currentSort === 'sentiment_label' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-[#066466] transition-colors">
                                        Sentimen
                                        <svg class="w-4 h-4 {{ $currentSort === 'sentiment_label' ? 'text-[#066466]' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'sentiment_label' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                        </svg>
                                    </a>
                                    <div class="relative group cursor-pointer inline-flex items-center">
                                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                            <div class="space-y-2">
                                                <div>
                                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                    <p class="text-slate-200 font-normal font-sans">Label klasifikasi sentimen ulasan (Positif, Netral, atau Negatif) yang diprediksi oleh model machine learning.</p>
                                                </div>
                                                <div class="pt-1.5 border-t border-slate-800">
                                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                    <p class="text-slate-200 font-normal font-sans">Dashboard monitoring ulasan admin.</p>
                                                </div>
                                            </div>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="px-10 py-6 text-left">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'sentiment_confidence', 'sort_order' => ($currentSort === 'sentiment_confidence' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-[#066466] transition-colors">
                                        Confidence
                                        <svg class="w-4 h-4 {{ $currentSort === 'sentiment_confidence' ? 'text-[#066466]' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'sentiment_confidence' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                        </svg>
                                    </a>
                                    <div class="relative group cursor-pointer inline-flex items-center">
                                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                            <div class="space-y-2">
                                                <div>
                                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                    <p class="text-slate-200 font-normal font-sans">Tingkat akurasi kepercayaan/probabilitas model machine learning dalam menentukan label sentimen (rentang 0 s/d 1).</p>
                                                </div>
                                                <div class="pt-1.5 border-t border-slate-800">
                                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                    <p class="text-slate-200 font-normal font-sans">Halaman ulasan dan analitik sentimen.</p>
                                                </div>
                                            </div>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="px-10 py-6 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($currentSort === 'created_at' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Waktu
                                    <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'created_at' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse(($reviews ?? []) as $review)
                            <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-sidebar/5 rounded-full flex items-center justify-center text-sidebar text-xs font-bold border border-sidebar/10 shadow-sm overflow-hidden">
                                            <span class="opacity-70">{{ strtoupper(substr($review->reviewer_name ?? 'A', 0, 1)) }}</span>
                                        </div>
                                        <div class="flex flex-col gap-0.5">
                                            <div class="text-sm font-bold text-gray-700">{{ $review->reviewer_name }}</div>
                                            @php
                                                $isRegistered = $review->user && !empty($review->user->password) && (!empty($review->user->email) || !empty($review->user->name));
                                            @endphp
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                @if($isRegistered)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#E6F6F2] text-[#00A884] uppercase tracking-wide border border-[#00A884]/10">👤 User</span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wide border border-gray-100">👥 Guest</span>
                                                @endif
                                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">ID: {{ substr((string)$review->user_id, -6) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        @if(optional($review->destination)->images && count($review->destination->images) > 0)
                                            <img src="{{ image_url($review->destination->images[0]) }}" class="w-20 h-14 object-cover rounded-xl shadow-sm border border-gray-100">
                                        @else
                                            <div class="w-20 h-14 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="text-[14px] font-bold text-gray-800 max-w-[150px] truncate" title="{{ optional($review->destination)->name ?? '' }}">{{ optional($review->destination)->name ?? 'Umum' }}</div>
                                            <div class="text-[11px] text-gray-400 mt-0.5 truncate max-w-[120px]" title="{{ optional($review->destination)->location ?? '' }}">{{ optional($review->destination)->location ?? 'Lokasi tidak tersedia' }}</div>
                                        </div>
                                    </div>
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
                                        <p class="mt-2 text-[11px] text-gray-400 max-w-[150px] truncate" title="{{ $review->sentiment_reason }}">{{ $review->sentiment_reason }}</p>
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
                                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.reviews.destroy', $review->_id) }}', title: 'Hapus Ulasan', type: 'ulasan', name: {{ json_encode('dari ' . ($review->reviewer_name ?? 'Anonim')) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
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
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar flex flex-col">

                <div class="flex items-center justify-between px-10 pt-8 pb-6 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Detail Ulasan</h3>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Detail Ulasan</span>
                                        <p class="text-slate-200 font-normal leading-relaxed text-[11px]">Menampilkan rincian ulasan wisatawan, nilai rating (bintang), destinasi target, serta hasil analisis sentimen (Positif/Netral/Negatif) dari model machine learning.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !viewingReview" class="py-16 flex justify-center px-10">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>

                <div x-show="viewingReview" class="space-y-6 px-10 py-8 flex-1">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                        <div class="w-12 h-12 bg-sidebar/10 rounded-full flex items-center justify-center text-sidebar font-bold text-lg">
                            <span x-text="viewingReview ? viewingReview.reviewer_name?.charAt(0)?.toUpperCase() || 'A' : 'A'"></span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-gray-800 text-sm" x-text="viewingReview?.reviewer_name || 'Anonim'"></p>
                                <template x-if="viewingReview?.user_is_registered">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#E6F6F2] text-[#00A884] uppercase tracking-wide border border-[#00A884]/10">👤 User</span>
                                </template>
                                <template x-if="!viewingReview?.user_is_registered">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wide border border-gray-100">👥 Guest</span>
                                </template>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="viewingReview?.created_at ? new Date(viewingReview.created_at).toLocaleDateString('id-ID', {year:'numeric', month:'long', day:'numeric'}) : ''"></p>
                        </div>
                        <div class="ml-auto text-2xl text-yellow-400" x-text="stars(viewingReview?.rating || 0)"></div>
                    </div>

                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                        <label class="text-[10px] font-bold text-blue-500 uppercase tracking-widest block mb-1">Destinasi</label>
                        <p class="text-sm font-bold text-blue-900" x-text="viewingReview?.destination?.name || 'Tidak diketahui'"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-4 bg-gray-50 rounded-2xl">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Sentimen</label>
                            <p class="text-sm font-bold text-gray-800" x-text="viewingReview?.sentiment_label ? viewingReview.sentiment_label.charAt(0).toUpperCase() + viewingReview.sentiment_label.slice(1) : 'Pending'"></p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Confidence</label>
                            <p class="text-sm font-bold text-gray-800" x-text="viewingReview?.sentiment_confidence ? Number(viewingReview.sentiment_confidence).toFixed(2) : '-' "></p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Reason</label>
                            <p class="text-sm font-bold text-gray-800 truncate" x-text="viewingReview?.sentiment_reason || '-' "></p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Ulasan</label>
                        <p class="text-sm text-gray-700 font-medium leading-relaxed p-4 bg-gray-50 rounded-2xl whitespace-pre-line" x-text="viewingReview?.review || '-'"></p>
                    </div>
                </div>

                <div x-show="viewingReview" class="px-10 py-6 bg-gray-50 flex items-center justify-end border-t border-gray-100">
                    <button @click="showViewModal = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div x-show="showExportModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showExportModal = false"></div>

            <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar flex flex-col">

                <div class="flex items-center justify-between px-10 pt-8 pb-6 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900">Konfigurasi Cetak (Kop Surat)</h3>
                            <p class="text-xs text-gray-400 font-medium mt-0.5">Sesuaikan informasi untuk kop surat dan penandatangan dokumen PDF analitik ulasan.</p>
                        </div>
                        <div class="relative group cursor-pointer inline-flex items-center mt-1">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Cetak Analitik</span>
                                        <p class="text-slate-200 font-normal leading-relaxed text-[11px]">Mengonfigurasi kop surat resmi dinas pariwisata dan penandatangan untuk berkas laporan PDF analitik sentimen ulasan.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="showExportModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form x-ref="exportForm" action="{{ route('admin.reviews.print-analytics') }}" method="POST" target="_blank" enctype="multipart/form-data" @submit="setTimeout(() => showExportModal = false, 100)" class="px-10 pb-8 space-y-6 flex-1">
                    @csrf
                    <!-- Format Export section (hidden for PDF-only analytics export) -->
                    <input type="hidden" name="exportFormat" value="pdf">
                    
                    <div class="space-y-5">
                        <!-- Custom Logo -->
                        <div class="space-y-1.5 p-4 bg-gray-50 border border-gray-100 rounded-xl">
                            <label class="text-xs font-bold text-gray-700 block">Logo Instansi Kustom (Opsional)</label>
                            <p class="text-xs text-gray-400 mb-2">Upload logo baru jika Anda ingin mengganti logo dari Pengaturan hanya untuk dokumen ini.</p>
                            <input type="file" name="custom_logo" accept="image/png, image/jpeg, image/jpg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                        </div>

                        <!-- Instansi -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-500 block">Nama Instansi (Pisahkan baris dengan /)</label>
                            <input type="text" name="instansi" x-model="instansi" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nomor Surat -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Nomor Surat Dinas</label>
                                <input type="text" name="nomor_surat" x-model="nomor_surat" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                            <!-- Perihal / Hal -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Perihal / Judul Surat</label>
                                <input type="text" name="hal" x-model="hal" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Nama Penandatangan -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Nama Pejabat</label>
                                <input type="text" name="nama_penandatangan" x-model="nama_penandatangan" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                            <!-- NIP -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">NIP Pejabat</label>
                                <input type="text" name="nip_penandatangan" x-model="nip_penandatangan" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                            <!-- Jabatan -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Jabatan Pejabat</label>
                                <input type="text" name="jabatan" x-model="jabatan" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-500 block">Alamat Lengkap Dinas</label>
                            <input type="text" name="alamat" x-model="alamat" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Email -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Email Dinas</label>
                                <input type="text" name="email" x-model="email" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                            <!-- Telp -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">No. Telpon Dinas</label>
                                <input type="text" name="telp" x-model="telp" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                            <!-- Website -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Website Dinas</label>
                                <input type="text" name="website" x-model="website" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/20 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 bg-gray-50 px-10 py-6 -mx-10 -mb-8">
                        <button type="button" @click="showExportModal = false" class="px-8 py-3 text-sm font-bold text-gray-600 border border-gray-200 rounded-2xl hover:bg-gray-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-indigo-600 rounded-2xl hover:opacity-90 shadow-md shadow-indigo-600/10 transition-all">
                            Buat PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('charts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>
<script>
    let sentimentChart = null;
    let ratingChart = null;
    let destinationChart = null;

    window.initReviewCharts = function() {
        console.log('initReviewCharts: Rendering Chart.js charts');
        
        // Sentiment Trend Line Chart
        const trendCtx = document.getElementById('sentimentTrendChart');
        if (trendCtx) {
            if (sentimentChart) {
                sentimentChart.destroy();
            }
            const ctx = trendCtx.getContext('2d');
            const trendData = @json($sentimentTrends ?? []);
            const labels = trendData.map(d => d.month);
            const positiveData = trendData.map(d => d.positive);
            const neutralData = trendData.map(d => d.neutral);
            const negativeData = trendData.map(d => d.negative);

            sentimentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Positif',
                            data: positiveData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            fill: true
                        },
                        {
                            label: 'Netral',
                            data: neutralData,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            fill: true
                        },
                        {
                            label: 'Negatif',
                            data: negativeData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // Rating Bar Chart
        const rateCtx = document.getElementById('ratingBarChart');
        if (rateCtx) {
            if (ratingChart) {
                ratingChart.destroy();
            }
            const ctx = rateCtx.getContext('2d');
            ratingChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['5 ★', '4 ★', '3 ★', '2 ★', '1 ★'],
                    datasets: [{
                        data: [
                            {{ $ratingDistribution[5]['count'] ?? 0 }},
                            {{ $ratingDistribution[4]['count'] ?? 0 }},
                            {{ $ratingDistribution[3]['count'] ?? 0 }},
                            {{ $ratingDistribution[2]['count'] ?? 0 }},
                            {{ $ratingDistribution[1]['count'] ?? 0 }}
                        ],
                        backgroundColor: '#066466',
                        borderRadius: 8,
                        barThickness: 14
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { precision: 0 }
                        },
                        y: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Destination Sentiment Chart
        const destCtx = document.getElementById('destinationSentimentChart');
        if (destCtx) {
            if (destinationChart) {
                destinationChart.destroy();
            }
            const ctx = destCtx.getContext('2d');
            const destData = @json(array_slice($keywordSummary['destinations'], 0, 6));
            const labels = destData.map(d => d.destination_name || d.name || 'Destinasi');
            const positiveData = destData.map(d => d.sentiment_counts?.positive ?? 0);
            const neutralData = destData.map(d => d.sentiment_counts?.neutral ?? 0);
            const negativeData = destData.map(d => d.sentiment_counts?.negative ?? 0);

            destinationChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Positif',
                            data: positiveData,
                            backgroundColor: '#10b981',
                            borderRadius: 6
                        },
                        {
                            label: 'Netral',
                            data: neutralData,
                            backgroundColor: '#f59e0b',
                            borderRadius: 6
                        },
                        {
                            label: 'Negatif',
                            data: negativeData,
                            backgroundColor: '#ef4444',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { precision: 0 }
                        },
                        y: {
                            stacked: true,
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    };

    window.initReviewsWordCloud = function() {
        const keywords = @json($keywordSummary['overall']['top_keywords'] ?? []);
        const sentimentMap = @json($sentimentMap ?? []);
        
        console.log('WordCloud: Initializing with', keywords.length, 'keywords');
        
        const canvas = document.getElementById('word-cloud-canvas');
        if (canvas) {
            const renderCloud = () => {
                if (typeof WordCloud === 'undefined') {
                    console.error('WordCloud: Library wordcloud2.js not loaded!');
                    return;
                }

                const container = canvas.parentElement;
                const width = container.offsetWidth;
                const height = container.offsetHeight;
                
                if (width === 0 || height === 0) {
                    console.warn('WordCloud: Container has 0 dimensions, retrying...');
                    setTimeout(renderCloud, 500);
                    return;
                }

                canvas.width = width;
                canvas.height = height;

                const list = keywords.map(item => {
                    let size = 22 + (item.count * 5);
                    if (size > 90) size = 90; 
                    return [item.keyword, size];
                });

                try {
                    WordCloud(canvas, { 
                        list: list,
                        gridSize: 6,
                        weightFactor: 1.2,
                        fontFamily: "'Instrument Sans', sans-serif",
                        color: function(word) {
                            const sentiment = sentimentMap[word] || 'neutral';
                            const colors = {
                                'positive': '#10b981',
                                'neutral': '#94a3b8',
                                'negative': '#f43f5e'
                            };
                            return colors[sentiment] || '#94a3b8';
                        },
                        rotateRatio: 0,
                        backgroundColor: 'transparent',
                        ellipticity: 0.65,
                        shuffle: true,
                        clearCanvas: true,
                        drawOutOfBound: false,
                        shrinkToFit: true
                    });
                    console.log('WordCloud: Rendered successfully');
                } catch (e) {
                    console.error('WordCloud: Render failed', e);
                }
            };

            renderCloud();
        }
    };

    // Re-render WordCloud and resize charts on window resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (typeof window.initReviewsWordCloud === 'function') {
                window.initReviewsWordCloud();
            }
        }, 500);
    });
</script>
@endpush
