@extends('admin.layouts.app')

@section('title', 'Ulasan Pengguna')
@section('navbar_title', 'Ulasan')
@section('page_title', 'Ulasan Pengguna')
@section('page_description', 'Moderasi dan analisis ulasan pengguna')

@section('page_actions')
<div class="flex items-center gap-4">
    <!-- Cetak Analitik (PDF) -->
  <div class="flex items-center gap-2">
    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-export-modal'))" class="flex items-center gap-2 px-8 py-3 bg-purple-800 text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-purple-800/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Cetak Analitik (PDF)
    </button>
    
    <div class="relative group cursor-pointer inline-flex items-center">
        <svg class="w-4 h-4 text-gray-400 hover:text-purple-800 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        
        <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
            <div class="space-y-2">
                <div>
                    <span class="block font-bold text-purple-500 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Cetak Analitik</span>
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
            <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
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
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Ulasan Pengguna</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold" id="breadcrumb-active-tab">Ringkasan Ulasan</span>
</nav>
@endsection

@section('content')

@if (!app_setting('enable_reviews', true))
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200/40 rounded-2xl flex items-start gap-3">
        <div class="flex-shrink-0 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center text-white mt-0.5">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-amber-800">Sistem Ulasan Dinonaktifkan</p>
            <p class="text-xs text-amber-700 mt-1">Wisatawan tidak dapat memberikan ulasan atau rating bintang baru pada halaman detail destinasi di situs publik (frontend).</p>
        </div>
    </div>
@elseif (app_setting('moderate_reviews', false))
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200/40 rounded-2xl flex items-start gap-3">
        <div class="flex-shrink-0 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white mt-0.5">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-blue-800">Moderasi Ulasan Aktif</p>
            <p class="text-xs text-blue-700 mt-1">Setiap ulasan baru dari wisatawan disaring dan harus disetujui terlebih dahulu oleh admin/moderator sebelum dipublikasikan ke publik.</p>
        </div>
    </div>
@endif

<div id="reviews-page-root"
     x-on:open-export-modal.window="showExportModal = true"
     x-on:open-confirm-modal.window="openConfirmDialog($event.detail.message, $event.detail.action, $event.detail.cancel, $event.detail.type || 'info', $event.detail.title || 'Konfirmasi')"
     x-data="{
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

    showConfirmModal: false,
    confirmMessage: '',
    confirmAction: null,
    cancelAction: null,
    confirmType: 'info',
    confirmTitle: 'Konfirmasi',

    openConfirmDialog(message, action, cancel, type = 'info', title = 'Konfirmasi') {
        this.confirmMessage = message;
        this.confirmAction = action;
        this.cancelAction = cancel;
        this.confirmType = type;
        this.confirmTitle = title;
        this.showConfirmModal = true;
    },
    executeConfirm() {
        if (this.confirmAction) this.confirmAction();
        this.showConfirmModal = false;
    },
    executeCancel() {
        if (this.cancelAction) this.cancelAction();
        this.showConfirmModal = false;
    },

    init() {
        this.updateBreadcrumb(this.activeTab);

        this.$watch('activeTab', value => {
            localStorage.setItem('active_review_tab', value);
            const url = new URL(window.location.href);
            url.searchParams.set('tab', value);
            window.history.replaceState({}, '', url.toString());

            this.updateBreadcrumb(value);

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

    updateBreadcrumb(tab) {
        const el = document.getElementById('breadcrumb-active-tab');
        if (el) {
            el.textContent = tab === 'list' ? 'Daftar Ulasan' : 'Ringkasan Ulasan';
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
        return '\u2605'.repeat(n) + '\u2606'.repeat(5 - n);
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

    <div x-show="activeTab === 'summary'" x-cloak class="space-y-8">

        {{-- â”€â”€â”€ FILTER BAR â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-5">
            <div class="flex flex-wrap items-end gap-4">
                {{-- Destinasi --}}
                <div class="flex-1 min-w-[180px] space-y-1.5">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Destinasi</label>
                    <select id="sf_destination"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-600 outline-none hover:border-sidebar transition-all cursor-pointer">
                        <option value="">Semua Destinasi</option>
                        @foreach($destinationsList as $dest)
                            <option value="{{ $dest->_id }}">{{ $dest->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Rating --}}
                <div class="w-36 space-y-1.5">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Rating</label>
                    <select id="sf_rating"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-600 outline-none hover:border-sidebar transition-all cursor-pointer">
                        <option value="">Semua</option>
                        <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; 5</option>
                        <option value="4">&#9733;&#9733;&#9733;&#9733;&#9734; 4</option>
                        <option value="3">&#9733;&#9733;&#9733;&#9734;&#9734; 3</option>
                        <option value="2">&#9733;&#9733;&#9734;&#9734;&#9734; 2</option>
                        <option value="1">&#9733;&#9734;&#9734;&#9734;&#9734; 1</option>
                    </select>
                </div>

                {{-- Sentimen --}}
                <div class="w-44 space-y-1.5">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Sentimen</label>
                    <select id="sf_sentiment"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-600 outline-none hover:border-sidebar transition-all cursor-pointer">
                        <option value="">Semua</option>
                        <option value="positive">Positif</option>
                        <option value="neutral">Netral</option>
                        <option value="negative">Negatif</option>
                        <option value="pending">Belum Dianalisis</option>
                    </select>
                </div>

                {{-- Tanggal dari --}}
                <div class="w-40 space-y-1.5">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Dari</label>
                    <input type="date" id="sf_date_from"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-600 outline-none hover:border-sidebar transition-all">
                </div>

                {{-- Tanggal sampai --}}
                <div class="w-40 space-y-1.5">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Sampai</label>
                    <input type="date" id="sf_date_to"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-600 outline-none hover:border-sidebar transition-all">
                </div>

                {{-- Tombol Terapkan & Reset --}}
                <div class="flex items-center gap-2 pt-5">
                    <button type="button" id="sf_apply"
                            class="px-6 py-2.5 bg-sidebar text-white rounded-xl text-sm font-bold hover:opacity-90 transition-all shadow-sm shadow-sidebar/20">
                        Terapkan
                    </button>
                    <button type="button" id="sf_reset"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                        Reset
                    </button>
                </div>

                {{-- Loading indicator --}}
                <div id="sf_loading" class="hidden items-center gap-2 pt-5">
                    <svg class="animate-spin w-5 h-5 text-sidebar" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-xs font-bold text-gray-400">Memuat data...</span>
                </div>
            </div>

            {{-- Active filter badges --}}
            <div id="sf_active_badges" class="hidden flex-wrap gap-2 mt-3 pt-3 border-t border-gray-50"></div>
        </div>
        {{-- â”€â”€â”€ END FILTER BAR â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[2rem] bg-white border border-gray-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Total Ulasan</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                <p class="mt-3 text-3xl font-black text-gray-900" id="sf_stat_total">{{ number_format($sentimentSummary['total']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-emerald-50 border border-emerald-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-emerald-500 font-bold">Ulasan Positif</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                <p class="mt-3 text-3xl font-black text-emerald-700" id="sf_stat_positive">{{ number_format($sentimentSummary['positive']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-amber-50 border border-amber-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-amber-500 font-bold">Ulasan Netral</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-amber-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                <p class="mt-3 text-3xl font-black text-amber-700" id="sf_stat_neutral">{{ number_format($sentimentSummary['neutral']) }}</p>
            </div>
            <div class="rounded-[2rem] bg-red-50 border border-red-100 shadow-sm p-5 relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-widest text-red-500 font-bold">Ulasan Negatif</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-red-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                <p class="mt-3 text-3xl font-black text-red-700" id="sf_stat_negative">{{ number_format($sentimentSummary['negative']) }}</p>
            </div>
        </div>

        {{-- ─── SENTIMEN WISATAWAN TERPOSITIF ────────────────────────────── --}}
        @php
            $topDests = $topDestinationsByRatingSentiment ?? [];
        @endphp
        @if(!empty($topDests))
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Sentimen Wisatawan Terpositif</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Destinasi dengan ulasan paling positif dari wisatawan berdasarkan analisis model ML</p>
                </div>
                <span class="flex items-center gap-1.5 text-[11px] font-bold px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Berdasarkan Sentimen
                </span>
            </div>

            <div class="space-y-3">
                @foreach($topDests as $rank => $dest)
                @php
                    $score      = $dest['sentiment_score'];
                    $scoreColor = $score >= 50 ? 'text-emerald-600 bg-emerald-50 border-emerald-100'
                                : ($score >= 0  ? 'text-amber-600 bg-amber-50 border-amber-100'
                                               : 'text-red-600 bg-red-50 border-red-100');
                    $pctPos     = $dest['pct_positive'];
                    $pctNeg     = $dest['total_analyzed'] > 0 ? round($dest['negative'] / $dest['total_analyzed'] * 100) : 0;
                    $pctNeu     = max(0, 100 - $pctPos - $pctNeg);
                    $rankColors = ['bg-amber-400 text-white', 'bg-gray-400 text-white', 'bg-orange-400 text-white',
                                   'bg-sidebar/20 text-sidebar', 'bg-sidebar/10 text-sidebar'];
                    $rankColor  = $rankColors[$rank] ?? 'bg-gray-100 text-gray-500';
                @endphp
                <div class="flex items-center gap-4 p-4 bg-gray-50/40 border border-gray-100 rounded-2xl hover:bg-gray-50 hover:shadow-sm transition-all">

                    {{-- Rank Badge --}}
                    <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $rankColor }} flex items-center justify-center text-xs font-black">
                        {{ $rank + 1 }}
                    </div>

                    {{-- Thumbnail --}}
                    @if($dest['thumbnail'])
                        <img src="{{ image_url($dest['thumbnail']) }}" alt="{{ $dest['name'] }}"
                             class="flex-shrink-0 w-12 h-12 rounded-xl object-cover border border-gray-100 shadow-sm"
                             loading="lazy" onerror="this.style.display='none'">
                    @else
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        </div>
                    @endif

                    {{-- Info —  NO RATING DISPLAYED HERE --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $dest['name'] }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-sidebar/10 text-sidebar">
                                {{ ucfirst($dest['category']) }}
                            </span>
                        </div>

                        @if($dest['total_analyzed'] > 0)
                            {{-- Breakdown angka sentimen --}}
                            <div class="flex items-center gap-3 mt-1.5 text-[10px] font-semibold text-gray-500">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $dest['positive'] }} positif
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    {{ $dest['neutral'] }} netral
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    {{ $dest['negative'] }} negatif
                                </span>
                            </div>
                            {{-- Progress bar sentimen --}}
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex h-1.5 w-full max-w-[180px] bg-gray-100 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full" style="width:{{ $pctPos }}%"></div>
                                    <div class="bg-amber-400 h-full" style="width:{{ $pctNeu }}%"></div>
                                    <div class="bg-red-500 h-full"   style="width:{{ $pctNeg }}%"></div>
                                </div>
                                <span class="text-[10px] text-gray-400">{{ $dest['total_analyzed'] }} teranalisis</span>
                            </div>
                        @else
                            <p class="text-[10px] text-gray-400 mt-1 italic">Belum ada sentimen teranalisis</p>
                        @endif
                    </div>

                    {{-- Sentiment Score Badge --}}
                    <div class="flex-shrink-0 text-right">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-sm font-black border {{ $scoreColor }}">
                            @if($score >= 0)
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5H7z"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5H7z"/></svg>
                            @endif
                            {{ $score > 0 ? '+' : '' }}{{ $score }}
                        </span>
                        <p class="text-[9px] text-gray-400 mt-1 text-center font-medium">skor sentimen</p>
                    </div>
                </div>
                @endforeach
            </div>

            <p class="mt-4 text-[11px] text-gray-400 leading-relaxed">
                Skor sentimen = <strong>(ulasan positif − negatif) ÷ total teranalisis × 100</strong>.
                Rentang: −100 (semua negatif) hingga +100 (semua positif). Rating ada di modul Destinasi.
            </p>
        </div>
        @endif
        {{-- ─── END SENTIMEN WISATAWAN TERPOSITIF ─────────────────────────── --}}

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

        @php
            // Build sentiment map: keyword → sentiment label
            $sentimentMap = [];
            if (!empty($keywordSummary['overall']['top_keywords_by_sentiment'])) {
                foreach ($keywordSummary['overall']['top_keywords_by_sentiment'] as $sent => $kwList) {
                    foreach ($kwList as $kw) {
                        $k = trim($kw['keyword'] ?? '');
                        if ($k !== '') $sentimentMap[$k] = $sent;
                    }
                }
            }

            // Pre-compute all display data in PHP — nothing goes into JS reactive state
            $kdDests = [];
            foreach (($keywordSummary['destinations'] ?? []) as $dest) {
                $sc   = $dest['sentiment_counts'] ?? ['negative' => 0, 'neutral' => 0, 'positive' => 0];
                $tot  = array_sum($sc);
                $dom  = 'neutral'; $mx = 0;
                foreach ($sc as $s => $c) { if ($c > $mx) { $mx = $c; $dom = $s; } }

                // keyword badge CSS classes resolved in PHP — no JS class binding needed
                $kwClassMap = ['positive' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                               'negative' => 'bg-red-50 text-red-700 border-red-100',
                               'neutral'  => 'bg-amber-50 text-amber-700 border-amber-100',
                               'other'    => 'bg-white text-gray-600 border-gray-100'];
                $domClassMap = ['positive' => 'bg-emerald-50 text-emerald-700',
                                'negative' => 'bg-red-50 text-red-700',
                                'neutral'  => 'bg-amber-50 text-amber-700'];
                $domLblMap   = ['positive' => 'Positif', 'negative' => 'Negatif', 'neutral' => 'Netral'];

                $keywords = [];
                foreach (array_slice($dest['top_keywords'] ?? [], 0, 8) as $kw) {
                    $word = $kw['keyword'] ?? '';
                    $sent = $sentimentMap[$word] ?? 'other';
                    $keywords[] = [
                        'word'  => $word,
                        'count' => $kw['count'] ?? 0,
                        'cls'   => $kwClassMap[$sent] ?? $kwClassMap['other'],
                    ];
                }

                // keyword words joined for data-attr search (lowercase, space-separated)
                $kwSearch = implode(' ', array_map(fn($k) => strtolower($k['word']), $keywords));

                $kdDests[] = [
                    'name'        => $dest['destination_name'] ?? $dest['name'] ?? $dest['destination_id'] ?? 'Destinasi',
                    'reviewCount' => $dest['review_count'] ?? 0,
                    'sc'          => $sc,
                    'tot'         => $tot,
                    'dom'         => $dom,
                    'domCls'      => $domClassMap[$dom] ?? $domClassMap['neutral'],
                    'domLbl'      => $domLblMap[$dom] ?? 'Netral',
                    'keywords'    => $keywords,
                    'kwSearch'    => $kwSearch,
                    // progress bar widths pre-computed
                    'wPos'        => $tot > 0 ? round($sc['positive'] / $tot * 100) : 0,
                    'wNeu'        => $tot > 0 ? round($sc['neutral']  / $tot * 100) : 0,
                    'wNeg'        => $tot > 0 ? round($sc['negative'] / $tot * 100) : 0,
                ];
            }
            $kdTotal  = count($kdDests);
            $kdLimit  = 6;
            $kdHidden = $kdTotal > $kdLimit; // whether "show more" button is needed
        @endphp

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6" id="kd-section">

            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Keyword Per Destinasi</h3>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500"
                      id="kd-count">{{ $kdTotal }} destinasi</span>
            </div>

            @if($kdTotal > 0)
                {{-- Search & filter — plain HTML, no Alpine binding --}}
                <div class="flex flex-wrap gap-3 mb-5">
                    <div class="flex-1 min-w-[180px] relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="kd-search" placeholder="Cari destinasi atau keyword..."
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-600 outline-none hover:border-sidebar">
                    </div>
                    <select id="kd-filter"
                            class="w-44 px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-600 outline-none hover:border-sidebar cursor-pointer">
                        <option value="all">Semua Sentimen</option>
                        <option value="positive">Positif Dominan</option>
                        <option value="neutral">Netral Dominan</option>
                        <option value="negative">Negatif Dominan</option>
                    </select>
                </div>

                {{-- Grid — rendered fully in PHP/Blade, zero JS binding --}}
                <div class="grid gap-4 lg:grid-cols-2" id="kd-grid">
                    @foreach($kdDests as $i => $d)
                        <div class="bg-gray-50/30 border border-gray-100 rounded-2xl p-5 kd-card"
                             data-name="{{ strtolower($d['name']) }}"
                             data-kw="{{ $d['kwSearch'] }}"
                             data-dom="{{ $d['dom'] }}"
                             @if($i >= $kdLimit) style="display:none" data-hidden="1" @endif>

                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 truncate">{{ $d['name'] }}</h4>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $d['reviewCount'] }} ulasan teranalisis</p>
                                </div>
                                <span class="flex-shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $d['domCls'] }}">
                                    {{ $d['domLbl'] }}
                                </span>
                            </div>

                            <div class="flex h-1.5 w-full bg-gray-100 rounded-full overflow-hidden mb-3">
                                <div class="bg-emerald-500" style="width:{{ $d['wPos'] }}%"></div>
                                <div class="bg-amber-400"   style="width:{{ $d['wNeu'] }}%"></div>
                                <div class="bg-red-500"     style="width:{{ $d['wNeg'] }}%"></div>
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                @forelse($d['keywords'] as $kw)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg border text-[11px] font-medium {{ $kw['cls'] }}">
                                        {{ $kw['word'] }}<span class="ml-1 text-[9px] font-bold opacity-40">{{ $kw['count'] }}</span>
                                    </span>
                                @empty
                                    <span class="text-[11px] text-gray-400 italic">Belum ada keyword</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="kd-empty" class="hidden py-10 text-center">
                    <p class="text-sm text-gray-400">Destinasi tidak ditemukan.</p>
                </div>

                @if($kdHidden)
                <div id="kd-more-wrap" class="mt-5 text-center">
                    <button type="button" id="kd-more"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-bold">
                        <span id="kd-more-label">Tampilkan Semua ({{ $kdTotal }})</span>
                        <svg id="kd-more-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                @endif

            @else
                <div class="py-12 text-center">
                    <p class="text-sm text-gray-400">Data keyword per destinasi belum tersedia.</p>
                </div>
            @endif
        </div>

        {{-- Vanilla JS — only search/filter/toggle logic, no reactive framework --}}
        <script>
        (function () {
            var searchEl  = document.getElementById('kd-search');
            var filterEl  = document.getElementById('kd-filter');
            var grid      = document.getElementById('kd-grid');
            var countEl   = document.getElementById('kd-count');
            var emptyEl   = document.getElementById('kd-empty');
            var moreWrap  = document.getElementById('kd-more-wrap');
            var moreBtn   = document.getElementById('kd-more');
            var moreLabel = document.getElementById('kd-more-label');
            var moreIcon  = document.getElementById('kd-more-icon');

            if (!grid) return;

            var LIMIT    = {{ $kdLimit }};
            var showAll  = false;
            var timer    = null;

            function applyFilter() {
                var q   = searchEl ? searchEl.value.toLowerCase().trim() : '';
                var sf  = filterEl ? filterEl.value : 'all';
                var cards = grid.querySelectorAll('.kd-card');
                var visible = 0;

                cards.forEach(function (card) {
                    var nameMatch = !q || card.dataset.name.indexOf(q) !== -1;
                    var kwMatch   = !q || card.dataset.kw.indexOf(q) !== -1;
                    var sentMatch = sf === 'all' || card.dataset.dom === sf;
                    var pass      = (nameMatch || kwMatch) && sentMatch;

                    if (pass) {
                        visible++;
                        // respect show-all toggle when no search/filter active
                        var overLimit = !q && sf === 'all' && !showAll && visible > LIMIT;
                        card.style.display = overLimit ? 'none' : '';
                        card.dataset.hidden = overLimit ? '1' : '0';
                    } else {
                        card.style.display = 'none';
                        card.dataset.hidden = '1';
                    }
                });

                if (countEl) countEl.textContent = visible + ' destinasi';
                if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);

                // Show/hide "show more" button
                var isFiltering = q || sf !== 'all';
                if (moreWrap) {
                    moreWrap.style.display = (!isFiltering && visible > LIMIT && !showAll) || (!isFiltering && showAll && {{ $kdTotal }} > LIMIT) ? '' : 'none';
                    if (moreLabel) moreLabel.textContent = showAll ? 'Tampilkan Lebih Sedikit' : 'Tampilkan Semua (' + visible + ')';
                    if (moreIcon)  moreIcon.style.transform = showAll ? 'rotate(180deg)' : '';
                }
            }

            if (searchEl) {
                searchEl.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(applyFilter, 250);
                });
            }

            if (filterEl) {
                filterEl.addEventListener('change', function () {
                    showAll = false;
                    applyFilter();
                });
            }

            if (moreBtn) {
                moreBtn.addEventListener('click', function () {
                    showAll = !showAll;
                    applyFilter();
                });
            }
        })();
        </script>
    </div>

    <div x-show="activeTab === 'list'" x-cloak class="space-y-8">
        {{-- Filter & Search --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex-1">
                    <!-- Persist current sorting and tab -->
                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                    <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                    <input type="hidden" name="tab" value="list">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                        <!-- Cari Ulasan -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Cari Ulasan
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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

                        <!-- Destinasi Wisata -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Destinasi Wisata
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Menyaring ulasan yang ditulis khusus untuk destinasi wisata tertentu.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </label>
                            <select name="destination_id" onchange="this.form.submit()"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                                <option value="">Semua Destinasi</option>
                                @foreach(($destinationsList ?? []) as $dest)
                                    <option value="{{ $dest->_id }}" @selected(request('destination_id') == $dest->_id)>{{ $dest->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sentimen -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Sentimen Ulasan
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Menyaring ulasan berdasarkan hasil klasifikasi sentimen.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </label>
                            <select name="sentiment" onchange="this.form.submit()"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                                <option value="">Semua Sentimen</option>
                                <option value="positive" @selected(request('sentiment') === 'positive')>Positif</option>
                                <option value="neutral" @selected(request('sentiment') === 'neutral')>Netral</option>
                                <option value="negative" @selected(request('sentiment') === 'negative')>Negatif</option>
                                <option value="pending" @selected(request('sentiment') === 'pending')>Pending</option>
                            </select>
                        </div>

                        <!-- Rating Bintang -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Rating Bintang
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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

                        <!-- Kesesuaian (Mismatch) -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Kesesuaian
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Menyaring ulasan yang memiliki ketidakcocokan antara rating bintang (tinggi/rendah) dan sentimen yang terdeteksi oleh model.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </label>
                            <select name="mismatch" onchange="this.form.submit()"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                                <option value="">Semua Data</option>
                                <option value="true" @selected(request('mismatch') === 'true' || request('mismatch') === '1')>Hanya Mismatch</option>
                            </select>
                        </div>

                        <!-- Tampilkan -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                Tampilkan
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                    </div>
                                </div>
                            </label>
                            <select name="per_page" onchange="this.form.submit()"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                                @foreach([10, 15, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }} Baris</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Batch Analysis & Reset -->
                <div class="flex items-center gap-3 lg:mb-[2px] mt-4 lg:mt-0 flex-shrink-0">
                    @if(request('search') || request('rating') || request('destination_id') || request('sentiment') || request('mismatch') || request('per_page') != 15)
                        <a href="{{ route('admin.reviews.index', ['tab' => 'list']) }}" class="px-5 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                            Reset
                        </a>
                    @endif

                    <form method="POST" action="{{ route('admin.reviews.analyze-batch') }}" class="m-0">
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
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden review-table-wrap">
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
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                            <tr class="hover:bg-gray-50/20 transition-colors border-b border-gray-50 last:border-0">
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
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#E6F6F2] text-[#00A884] uppercase tracking-wide border border-[#00A884]/10">
                                                        <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                                                        User
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wide border border-gray-100">
                                                        <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h12v-1a7 7 0 00-7-7z"></path></svg>
                                                        Guest
                                                    </span>
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
                                    <select onfocus="this.dataset.prev = this.value"
                                        onchange="confirmSentimentChange('{{ $review->_id }}', this, {{ $review->rating ?? 0 }})"
                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold border border-gray-100/10 outline-none cursor-pointer transition-all shadow-sm focus:ring-2 focus:ring-sidebar/20
                                            {{ $review->sentiment_label === 'positive' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100/50' : '' }}
                                            {{ $review->sentiment_label === 'neutral' ? 'bg-amber-50 text-amber-700 border-amber-100 hover:bg-amber-100/50' : '' }}
                                            {{ $review->sentiment_label === 'negative' ? 'bg-red-50 text-red-700 border-red-100 hover:bg-red-100/50' : '' }}
                                            {{ empty($review->sentiment_label) ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : '' }}">
                                        <option value="" disabled @selected(empty($review->sentiment_label))>Pending</option>
                                        <option value="positive" @selected($review->sentiment_label === 'positive')>Positif</option>
                                        <option value="neutral" @selected($review->sentiment_label === 'neutral')>Netral</option>
                                        <option value="negative" @selected($review->sentiment_label === 'negative')>Negatif</option>
                                    </select>
                                    <div class="mt-1 flex flex-col gap-0.5" id="sentiment-reason-wrap-{{ $review->_id }}">
                                        @if(!empty($review->sentiment_reason))
                                            <span class="text-[10px] font-semibold text-gray-400" title="{{ $review->sentiment_reason }}">
                                                {{ $review->sentiment_reason === 'manual_correction' ? '🔧 Dikoreksi Manual' : 'Model: ' . $review->sentiment_reason }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-10 py-6" id="confidence-container-{{ $review->_id }}">
                                    @if($review->sentiment_reason === 'manual_correction')
                                        {{-- Koreksi manual: bukan hasil model, tampilkan badge khusus --}}
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-500 text-[11px] font-semibold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Dikoreksi Manual
                                        </span>
                                    @elseif(isset($review->sentiment_confidence))
                                        @php
                                            $conf = (float) $review->sentiment_confidence;
                                            $barColor = $conf >= 0.75 ? 'bg-emerald-500' : ($conf >= 0.50 ? 'bg-amber-500' : 'bg-rose-500');
                                            $textColor = $conf >= 0.75 ? 'text-emerald-700' : ($conf >= 0.50 ? 'text-amber-700' : 'text-rose-700');
                                        @endphp
                                        <div class="flex flex-col gap-1.5 w-20">
                                            <span class="text-sm font-bold {{ $textColor }}" id="confidence-value-{{ $review->_id }}">
                                                {{ number_format($conf, 2) }}
                                            </span>
                                            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                <div class="h-full {{ $barColor }} rounded-full" id="confidence-bar-{{ $review->_id }}" style="width: {{ $conf * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm font-semibold text-gray-400">-</span>
                                    @endif
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
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#E6F6F2] text-[#00A884] uppercase tracking-wide border border-[#00A884]/10">
                                                        <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                                                        User
                                                    </span>
                                                </template>
                                                <template x-if="!viewingReview?.user_is_registered">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wide border border-gray-100">
                                                        <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h12v-1a7 7 0 00-7-7z"></path></svg>
                                                        Guest
                                                    </span>
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
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900 text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
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
    <!-- Custom Confirm Modal (Alpine-driven, inside Alpine scope) REMOVED -->
</div>
@endsection

{{-- STANDALONE SENTIMENT CONFIRM MODAL: pure DOM, outside Alpine, always works --}}
<div id="sentiment-confirm-overlay"
     style="display:none"
     class="fixed inset-0 z-[210] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
     onclick="if(event.target===this){window._sentimentCancelFn&&window._sentimentCancelFn();this.style.display='none';}"
>
    <div id="sco-box" class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl text-center overflow-hidden">
        <div class="px-8 pt-8 pb-4">
            <div id="sco-icon-warning" style="display:none" class="w-[72px] h-[72px] bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div id="sco-icon-info" style="display:none" class="w-[72px] h-[72px] bg-[#E6F6F2] rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-[#066466]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
            <h3 id="sco-title" class="text-[20px] font-bold text-gray-900 mb-2">Konfirmasi</h3>
            <p id="sco-message" class="text-[14px] text-gray-500 leading-relaxed px-2"></p>
            <div id="sco-warning-note" style="display:none" class="mt-4 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-[12px] font-semibold text-amber-700 text-left flex items-start gap-2">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Ketidaksesuaian antara rating dan sentimen dapat mempengaruhi akurasi analisis destinasi.</span>
            </div>
        </div>
        <div class="flex items-center gap-3 px-8 pb-8 pt-4">
            <button type="button"
                onclick="document.getElementById('sentiment-confirm-overlay').style.display='none'; window._sentimentCancelFn && window._sentimentCancelFn();"
                class="flex-1 py-3 text-[14px] font-bold text-gray-700 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 transition-all">
                Batal
            </button>
            <button type="button" id="sco-confirm-btn"
                onclick="document.getElementById('sentiment-confirm-overlay').style.display='none'; window._sentimentConfirmFn && window._sentimentConfirmFn();"
                class="flex-1 py-3 text-[14px] font-bold text-white rounded-2xl hover:opacity-90 transition-all">
                Ya, Ubah
            </button>
        </div>
    </div>
</div>

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
                    labels: ['5 \u2605', '4 \u2605', '3 \u2605', '2 \u2605', '1 \u2605'],
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
        const canvas = document.getElementById('word-cloud-canvas');
        if (!canvas) return;

        const doRender = () => {
            if (typeof WordCloud === 'undefined') return;
            const container = canvas.parentElement;
            const width = container.offsetWidth;
            const height = container.offsetHeight;
            if (width === 0 || height === 0) { setTimeout(doRender, 500); return; }

            canvas.width = width;
            canvas.height = height;

            const keywords   = @json($keywordSummary['overall']['top_keywords'] ?? []);
            const sentimentMap = @json($sentimentMap ?? []);
            const sentColors = { positive: '#10b981', neutral: '#94a3b8', negative: '#f43f5e' };

            const list = keywords.map(item => {
                let size = 22 + (item.count * 5);
                if (size > 90) size = 90;
                return [item.keyword, size];
            });

            try {
                WordCloud(canvas, {
                    list, gridSize: 8, weightFactor: 1.2,
                    fontFamily: "'Instrument Sans', sans-serif",
                    color: w => sentColors[sentimentMap[w] || 'neutral'] || '#94a3b8',
                    rotateRatio: 0, backgroundColor: 'transparent',
                    ellipticity: 0.65, shuffle: false,
                    clearCanvas: true, drawOutOfBound: false, shrinkToFit: true
                });
            } catch (e) {}
        };

        // Defer to idle time — never blocks scroll/paint
        if (typeof requestIdleCallback !== 'undefined') {
            requestIdleCallback(doRender, { timeout: 2000 });
        } else {
            setTimeout(doRender, 300);
        }
    };

    // Re-render WordCloud on window resize (debounced)
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (typeof window.initReviewsWordCloud === 'function') window.initReviewsWordCloud();
        }, 600);
    }, { passive: true });

    // â”€â”€â”€ SUMMARY FILTER LOGIC â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    (function () {
        const STATS_URL = '{{ route("admin.reviews.summary-stats") }}';

        // Collect current filter values
        function getFilters() {
            return {
                destination_id : document.getElementById('sf_destination')?.value || '',
                rating         : document.getElementById('sf_rating')?.value || '',
                sentiment      : document.getElementById('sf_sentiment')?.value || '',
                date_from      : document.getElementById('sf_date_from')?.value || '',
                date_to        : document.getElementById('sf_date_to')?.value || '',
            };
        }

        // Show/hide loading and apply button
        function setLoading(on) {
            const loading = document.getElementById('sf_loading');
            const btn     = document.getElementById('sf_apply');
            if (loading) loading.classList.toggle('hidden', !on);
            if (loading) loading.classList.toggle('flex', on);
            if (btn) btn.disabled = on;
        }

        // Render active filter badges
        function renderBadges(filters) {
            const wrap = document.getElementById('sf_active_badges');
            if (!wrap) return;
            wrap.innerHTML = '';
            const labels = {
                destination_id : 'Destinasi',
                rating         : 'Rating',
                sentiment      : 'Sentimen',
                date_from      : 'Dari',
                date_to        : 'Sampai',
            };
            const sentimentLabels = { positive: 'Positif', neutral: 'Netral', negative: 'Negatif', pending: 'Belum Dianalisis' };
            let hasAny = false;
            Object.entries(filters).forEach(([key, val]) => {
                if (!val) return;
                hasAny = true;
                let display = val;
                if (key === 'destination_id') {
                    const sel = document.getElementById('sf_destination');
                    display = sel?.options[sel.selectedIndex]?.text || val;
                }
                if (key === 'sentiment') display = sentimentLabels[val] || val;
                if (key === 'rating') display = `${val} \u2605`;
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center gap-1 px-3 py-1 bg-sidebar/10 text-sidebar text-xs font-bold rounded-full';
                badge.innerHTML = `${labels[key]}: ${display}
                    <button onclick="this.parentElement.remove(); document.getElementById('sf_${key}').value=''; applyFilters();" class="ml-1 hover:text-red-500 transition-colors">&times;</button>`;
                wrap.appendChild(badge);
            });
            wrap.classList.toggle('hidden', !hasAny);
            wrap.classList.toggle('flex', hasAny);
        }

        // Update stat cards from response
        function updateStatCards(data) {
            const s = data.sentiment ?? {};
            const map = {
                'sf_stat_total'    : data.total ?? 0,
                'sf_stat_positive' : s.positive ?? 0,
                'sf_stat_neutral'  : s.neutral ?? 0,
                'sf_stat_negative' : s.negative ?? 0,
                'sf_stat_pending'  : s.pending ?? 0,
                'sf_stat_avg'      : data.avg_rating ?? 0,
            };
            Object.entries(map).forEach(([id, val]) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            });
        }

        // Rebuild all Chart.js charts with fresh data
        function updateCharts(data) {
            // Sentiment Trend Chart
            if (sentimentChart && data.sentiment_trends) {
                const t = data.sentiment_trends;
                sentimentChart.data.labels                     = t.map(d => d.month);
                sentimentChart.data.datasets[0].data           = t.map(d => d.positive);
                sentimentChart.data.datasets[1].data           = t.map(d => d.neutral);
                sentimentChart.data.datasets[2].data           = t.map(d => d.negative);
                sentimentChart.update();
            }

            // Rating Bar Chart
            if (ratingChart && data.rating_dist) {
                const rd = data.rating_dist;
                ratingChart.data.datasets[0].data = [
                    rd[5]?.count ?? 0,
                    rd[4]?.count ?? 0,
                    rd[3]?.count ?? 0,
                    rd[2]?.count ?? 0,
                    rd[1]?.count ?? 0,
                ];
                ratingChart.update();
            }

            // Destination Sentiment Chart
            if (destinationChart && data.destinations?.length) {
                const dests = data.destinations.slice(0, 8);
                destinationChart.data.labels             = dests.map(d => d.destination_name || 'Destinasi');
                destinationChart.data.datasets[0].data   = dests.map(d => d.sentiment_counts?.positive ?? 0);
                destinationChart.data.datasets[1].data   = dests.map(d => d.sentiment_counts?.neutral ?? 0);
                destinationChart.data.datasets[2].data   = dests.map(d => d.sentiment_counts?.negative ?? 0);
                destinationChart.update();
            }
        }

        // Main fetch & update function
        window.applyFilters = async function () {
            const filters = getFilters();
            renderBadges(filters);
            setLoading(true);
            try {
                const params = new URLSearchParams(Object.fromEntries(
                    Object.entries(filters).filter(([, v]) => v)
                ));
                const res  = await fetch(`${STATS_URL}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    updateStatCards(data);
                    updateCharts(data);
                }
            } catch (e) {
                console.error('Filter error:', e);
            } finally {
                setLoading(false);
            }
        };

        // Wire up buttons
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('sf_apply')?.addEventListener('click', window.applyFilters);

            document.getElementById('sf_reset')?.addEventListener('click', () => {
                ['sf_destination', 'sf_rating', 'sf_sentiment'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                ['sf_date_from', 'sf_date_to'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                window.applyFilters();
            });

            // Auto-apply on select change (optional UX)
            ['sf_destination', 'sf_rating', 'sf_sentiment'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', window.applyFilters);
            });
        });
    })();
    // ─── END SUMMARY FILTER LOGIC ─────────────────────────────────

    // --- INLINE SENTIMENT UPDATE AJAX ---
    window.updateSentimentInline = async function(reviewId, newSentiment, selectEl) {
        selectEl = selectEl || (window.event ? window.event.target : null);
        try {
            if (selectEl) {
                selectEl.style.opacity = '0.5';
                selectEl.disabled = true;
            }

            const res = await fetch(`/admin/reviews/${reviewId}/update-sentiment`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ sentiment: newSentiment })
            });

            if (!res.ok) {
                throw new Error('Gagal memperbarui sentimen.');
            }

            const resData = await res.json();
            if (resData.success) {
                if (selectEl) {
                    selectEl.className = 'inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold border border-gray-100/10 outline-none cursor-pointer transition-all shadow-sm focus:ring-2 focus:ring-sidebar/20 ' +
                        (newSentiment === 'positive' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100/50' : '') +
                        (newSentiment === 'neutral' ? 'bg-amber-50 text-amber-700 border-amber-100 hover:bg-amber-100/50' : '') +
                        (newSentiment === 'negative' ? 'bg-red-50 text-red-700 border-red-100 hover:bg-red-100/50' : '');
                }

                // Update reason text
                const reasonWrap = document.getElementById(`sentiment-reason-wrap-${reviewId}`);
                if (reasonWrap) {
                    reasonWrap.innerHTML = '<span class="text-[10px] font-semibold text-gray-400">🔧 Dikoreksi Manual</span>';
                }

                // Ganti area confidence dengan badge "Manual" — bukan hasil model, jangan tampilkan angka
                const confContainer = document.getElementById(`confidence-container-${reviewId}`);
                if (confContainer) {
                    confContainer.innerHTML =
                        '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-500 text-[11px] font-semibold">' +
                        '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' +
                        'Dikoreksi Manual' +
                        '</span>';
                }

                window.showAlert('Sentimen berhasil diperbarui!', 'Sukses', 'success');
            } else {
                throw new Error(resData.message || 'Respons tidak valid.');
            }
        } catch(e) {
            console.error('Error updating sentiment inline:', e);
            window.showAlert('Gagal memperbarui sentimen: ' + e.message, 'Error', 'error');
            setTimeout(() => { window.location.reload(); }, 1500);
        } finally {
            if (selectEl) {
                selectEl.style.opacity = '1';
                selectEl.disabled = false;
            }
        }
    };

    // --- STANDALONE CONFIRM MODAL HELPER (pure DOM, no Alpine dependency) ---
    window._sentimentConfirmFn = null;
    window._sentimentCancelFn  = null;

    window.showSentimentConfirm = function(message, type, title, onConfirm, onCancel) {
        var overlay = document.getElementById('sentiment-confirm-overlay');
        if (!overlay) return;

        // Set teks
        document.getElementById('sco-message').textContent = message;
        document.getElementById('sco-title').textContent   = title || 'Konfirmasi';

        // Toggle ikon
        var iconWarn = document.getElementById('sco-icon-warning');
        var iconInfo = document.getElementById('sco-icon-info');
        var warnNote = document.getElementById('sco-warning-note');
        var confirmBtn = document.getElementById('sco-confirm-btn');

        if (type === 'warning') {
            iconWarn.style.display = 'flex';
            iconInfo.style.display = 'none';
            // Tampilkan catatan kuning mismatch
            if (warnNote) warnNote.style.display = 'flex';
            // Tombol konfirmasi warna oranye/amber untuk mismatch
            if (confirmBtn) confirmBtn.style.cssText = 'background:#d97706; box-shadow: 0 4px 14px -4px rgba(217,119,6,0.5);';
        } else {
            iconWarn.style.display = 'none';
            iconInfo.style.display = 'flex';
            // Sembunyikan catatan mismatch
            if (warnNote) warnNote.style.display = 'none';
            // Tombol konfirmasi warna teal normal
            if (confirmBtn) confirmBtn.style.cssText = 'background:#066466; box-shadow: 0 4px 14px -4px rgba(6,100,102,0.4);';
        }

        window._sentimentConfirmFn = onConfirm || null;
        window._sentimentCancelFn  = onCancel  || null;
        overlay.style.display = 'flex';
    };

    // --- CONFIRM SENTIMENT CHANGE WITH RATING MISMATCH WARNING ---
    window.confirmSentimentChange = function(reviewId, selectEl, rating) {
        var originalValue = selectEl.dataset.prev || selectEl.value;
        var newValue      = selectEl.value;

        if (originalValue === newValue) return;

        var message = 'Apakah Anda yakin ingin mengubah sentimen ulasan ini?';
        var type    = 'info';
        var title   = 'Konfirmasi Perubahan';

        if (newValue === 'positive' && parseInt(rating) <= 2) {
            message = 'Ulasan ini memiliki rating rendah (' + rating + ' Bintang), tetapi Anda memilih sentimen Positif. Apakah Anda yakin ingin menyimpan perubahan ini?';
            type    = 'warning';
            title   = 'Peringatan: Mismatch';
        } else if (newValue === 'negative' && parseInt(rating) >= 4) {
            message = 'Ulasan ini memiliki rating tinggi (' + rating + ' Bintang), tetapi Anda memilih sentimen Negatif. Apakah Anda yakin ingin menyimpan perubahan ini?';
            type    = 'warning';
            title   = 'Peringatan: Mismatch';
        }

        window.showSentimentConfirm(
            message,
            type,
            title,
            function() {
                // Konfirmasi: lakukan update
                window.updateSentimentInline(reviewId, newValue, selectEl);
                selectEl.dataset.prev = newValue;
            },
            function() {
                // Batal: kembalikan nilai sebelumnya
                selectEl.value = originalValue;
            }
        );
    };
</script>
@endpush



