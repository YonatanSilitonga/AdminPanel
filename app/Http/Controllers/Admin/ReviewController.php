<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoReview;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewController extends BaseAdminController
{
    public function __construct(protected SentimentAnalysisService $sentimentService)
    {
        parent::__construct();
    }

    /**
     * Display list of reviews from MongoDB
     */
    public function index(Request $request)
    {
        try {
            $query = MongoReview::with(['destination', 'user']);

            // Filter by rating
            if ($request->filled('rating')) {
                $query->where('rating', (int)$request->rating);
            }

            // Search in review text
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('review', 'like', "%{$search}%");
            }

            // Pagination
            $perPage = (int) $request->input('per_page', 15);
            $allowedSorts = ['rating', 'created_at', 'sentiment_label', 'sentiment_confidence'];
            $sortBy = in_array($request->input('sort_by'), $allowedSorts) ? $request->input('sort_by') : 'created_at';
            $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';

            $reviews = $query->orderBy($sortBy, $sortOrder)
                ->paginate($perPage)
                ->withQueryString();

            $ratings = [1, 2, 3, 4, 5];
            
            // Cache the aggregation queries for 5 minutes to avoid 10 sequential DB hits
            $statsCacheKey = 'review_stats_summary';
            $statsData = \Illuminate\Support\Facades\Cache::remember($statsCacheKey, now()->addMinutes(5), function () {
                $total = MongoReview::count();
                $dist = [];
                foreach ([5, 4, 3, 2, 1] as $rating) {
                    $count = MongoReview::where('rating', $rating)->count();
                    $dist[$rating] = [
                        'count' => $count,
                        'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
                    ];
                }
                
                $sentiments = [
                    'total' => $total,
                    'positive' => MongoReview::where('sentiment_label', 'positive')->count(),
                    'neutral' => MongoReview::where('sentiment_label', 'neutral')->count(),
                    'negative' => MongoReview::where('sentiment_label', 'negative')->count(),
                    'pending' => MongoReview::whereNull('sentiment_label')->count(),
                ];
                
                return ['total' => $total, 'distribution' => $dist, 'sentiments' => $sentiments];
            });

            $totalReviews = $statsData['total'];
            $ratingDistribution = $statsData['distribution'];
            $sentimentSummary = $statsData['sentiments'];

            $keywordSummary = [
                'overall' => [
                    'review_count' => 0,
                    'sentiment_counts' => [
                        'negative' => 0,
                        'neutral' => 0,
                        'positive' => 0,
                    ],
                    'top_keywords' => [],
                    'top_keywords_by_sentiment' => [
                        'negative' => [],
                        'neutral' => [],
                        'positive' => [],
                    ],
                ],
                'destinations' => [],
            ];
            $predictionSummary = [];
            $keywordModelVersion = null;

            $cacheKey = 'review_keyword_summary_' . date('Y-m-d_H'); // Cache per jam
            $cachedSummary = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHours(2), function () {
                $reviewsForKeywords = MongoReview::whereNotNull('sentiment_label')
                    ->orderBy('created_at', 'desc')
                    ->limit(500)
                    ->get(['_id', 'destination_id', 'review']);

                if ($reviewsForKeywords->isNotEmpty()) {
                    $summaryPayload = $reviewsForKeywords->map(fn ($review) => [
                        'id' => (string) $review->_id,
                        'text' => (string) ($review->review ?? ''),
                        'destination_id' => $review->destination_id ? (string) $review->destination_id : null,
                    ])->values()->all();

                    return $this->sentimentService->summaryKeywords($summaryPayload, 50);
                }
                return null;
            });

            if ($cachedSummary && ($cachedSummary['success'] ?? false)) {
                $summaryData = $cachedSummary['data'] ?? [];
                $keywordSummary = $this->enrichKeywordSummaryWithDestinationNames($summaryData['keyword_summary'] ?? $keywordSummary);
                $predictionSummary = $summaryData['prediction_summary'] ?? [];
                $keywordModelVersion = $summaryData['model_version'] ?? null;
            }

            // Calculate trends for the last 6 months
            $trendsCacheKey = 'review_trends_6_months';
            $sentimentTrends = \Illuminate\Support\Facades\Cache::remember($trendsCacheKey, now()->addMinutes(5), function () {
                $trends = [];
                for ($i = 5; $i >= 0; $i--) {
                    $month = now()->subMonths($i);
                    $startDate = $month->copy()->startOfMonth();
                    $endDate = $month->copy()->endOfMonth();
                    
                    $positive = MongoReview::whereBetween('created_at', [$startDate, $endDate])
                        ->where('sentiment_label', 'positive')
                        ->count();
                    $neutral = MongoReview::whereBetween('created_at', [$startDate, $endDate])
                        ->where('sentiment_label', 'neutral')
                        ->count();
                    $negative = MongoReview::whereBetween('created_at', [$startDate, $endDate])
                        ->where('sentiment_label', 'negative')
                        ->count();
                        
                    $trends[] = [
                        'month' => $month->translatedFormat('M Y'),
                        'positive' => $positive,
                        'neutral' => $neutral,
                        'negative' => $negative,
                    ];
                }
                return $trends;
            });

            return view('admin.reviews.index', [
                'reviews' => $reviews,
                'ratings' => $ratings,
                'sentimentSummary' => $sentimentSummary,
                'ratingDistribution' => $ratingDistribution,
                'keywordSummary' => $keywordSummary,
                'predictionSummary' => $predictionSummary,
                'keywordModelVersion' => $keywordModelVersion,
                'sentimentTrends' => $sentimentTrends,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reviews from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error loading reviews from MongoDB');
        }
    }

    public function show(string $id, Request $request)
    {
        try {
            // Load review with relationship
            $review = MongoReview::with(['destination', 'user'])->findOrFail($id);

            // Handle AJAX/JSON requests
            if ($request->ajax() || $request->wantsJson()) {
                $reviewData = $review->toArray();
                $reviewData['user_is_registered'] = $review->user && !empty($review->user->password) && (!empty($review->user->email) || !empty($review->user->name));
                return response()->json($reviewData);
            }

            // Handle regular requests
            return view('admin.reviews.show', ['review' => $review]);
            
        } catch (ModelNotFoundException $e) {
            Log::warning('Review not found: ' . $id);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Review tidak ditemukan',
                ], 404);
            }
            return back()->with('error', 'Review tidak ditemukan');
            
        } catch (\Exception $e) {
            Log::error('Error loading review: ' . $e->getMessage(), [
                'review_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Gagal memuat detail ulasan',
                    'message' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
            
            return back()->with('error', 'Error loading review');
        }
    }

    public function analyze(string $id, Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $review = MongoReview::findOrFail($id);
            $result = $this->sentimentService->predict($review->review ?? '', (string) $review->_id);

            if (!($result['success'] ?? false)) {
                $message = $result['error'] ?? 'Sentiment analysis failed';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            $normalized = $this->normalizePrediction($result);
            if (!$this->passesQualityGate($normalized)) {
                $message = 'Hasil sentiment tidak lolos validasi kualitas (scores/confidence tidak konsisten).';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            $review->update([
                'sentiment_label' => $normalized['label'],
                'sentiment_confidence' => $normalized['confidence'],
                'sentiment_scores' => $normalized['scores'],
                'sentiment_reason' => $normalized['reason'],
                'sentiment_model_version' => $normalized['model_version'],
                'sentiment_analyzed_at' => now(),
            ]);

            $this->clearReviewCaches();

            $this->logActivity('analyze_review_sentiment', 'review', $id, null, [
                'sentiment_label' => $result['label'] ?? 'neutral',
                'sentiment_confidence' => $result['confidence'] ?? 0,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sentiment analyzed successfully',
                    'data' => $result,
                ]);
            }

            return back()->with('success', 'Sentiment ulasan berhasil dianalisis.');
        } catch (\Exception $e) {
            Log::error('Error analyzing review sentiment: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error analyzing sentiment'], 500);
            }

            return back()->with('error', 'Gagal menganalisis sentiment ulasan.');
        }
    }

    public function analyzeBatch(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $limit = min((int) $request->input('limit', 20), 100);
            $reviews = MongoReview::whereNull('sentiment_label')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            if ($reviews->isEmpty()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => 'Tidak ada ulasan pending.']);
                }

                return back()->with('info', 'Tidak ada ulasan yang perlu dianalisis.');
            }

            $payload = $reviews->map(fn ($review) => [
                'id' => (string) $review->_id,
                'text' => $review->review ?? '',
                'destination_id' => $review->destination_id ? (string) $review->destination_id : null,
            ])->values()->all();

            $results = $this->sentimentService->predictBatch($payload);

            if (!($results['success'] ?? false)) {
                $message = $results['error'] ?? 'Batch sentiment analysis failed';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            $predictions = $results['results'] ?? [];

            if (!is_array($predictions)) {
                $predictions = [];
            }

            $analyzed = 0;
            foreach ($predictions as $prediction) {
                if (!is_array($prediction)) {
                    continue;
                }

                // Some API variants don't include `success`; treat entries with label as successful
                $isSuccessful = ($prediction['success'] ?? null);
                if ($isSuccessful === null) {
                    $isSuccessful = isset($prediction['label']) || isset($prediction['sentiment_label']);
                }

                if (!$isSuccessful) {
                    continue;
                }

                $reviewId = $prediction['id'] ?? $prediction['review_id'] ?? null;
                if (empty($reviewId)) {
                    continue;
                }

                $normalized = $this->normalizePrediction($prediction);
                if (!$this->passesQualityGate($normalized)) {
                    continue;
                }

                $updated = MongoReview::where('_id', $reviewId)->update([
                    'sentiment_label' => $normalized['label'],
                    'sentiment_confidence' => $normalized['confidence'],
                    'sentiment_scores' => $normalized['scores'],
                    'sentiment_reason' => $normalized['reason'],
                    'sentiment_model_version' => $normalized['model_version'],
                    'sentiment_analyzed_at' => now(),
                ]);

                if ($updated > 0) {
                    $analyzed++;
                }
            }

            if ($analyzed > 0) {
                $this->clearReviewCaches();
            }

            $this->logActivity('batch_analyze_review_sentiment', 'review', null, null, [
                'requested_limit' => $limit,
                'analyzed' => $analyzed,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Batch sentiment analysis selesai.',
                    'analyzed' => $analyzed,
                    'total' => count($payload),
                ]);
            }

            return back()->with('success', "Sentiment $analyzed ulasan berhasil dianalisis.");
        } catch (\Exception $e) {
            Log::error('Error batch analyzing review sentiment: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error batch analyzing sentiment'], 500);
            }

            return back()->with('error', 'Gagal menganalisis sentiment ulasan.');
        }
    }

    /**
     * Delete review from MongoDB
     */
    public function destroy(string $id)
    {
        try {
            $review = MongoReview::findOrFail($id);
            $review->delete();

            $this->clearReviewCaches();

            $this->logActivity('delete_review_mongo', 'review', $id);

            return redirect()
                ->route('admin.reviews.index')
                ->with('success', 'Review deleted from MongoDB successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting review from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error deleting review');
        }
    }

    /**
     * Export reviews to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = MongoReview::with(['destination', 'user']);

            if ($request->filled('rating')) {
                $query->where('rating', (int)$request->rating);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('review', 'like', "%{$search}%");
            }

            $reviews = $query->orderBy('created_at', 'desc')->get();

            $filename = 'reviews_report_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($reviews) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                fputcsv($file, ['ID', 'User', 'Tipe User', 'Destinasi', 'Rating', 'Ulasan', 'Sentimen', 'Confidence', 'Tanggal'], ';');

                foreach ($reviews as $review) {
                    $isRegistered = $review->user && !empty($review->user->password) && (!empty($review->user->email) || !empty($review->user->name));
                    $userType = $isRegistered ? 'User' : 'Guest';
                    fputcsv($file, [
                        $review->_id,
                        $review->reviewer_name,
                        $userType,
                        $review->destination?->name ?? 'Umum',
                        $review->rating ?? 0,
                        $review->review ?? '-',
                        $review->sentiment_label ?? 'Pending',
                        $review->sentiment_confidence ?? 0,
                        $review->created_at?->format('d-m-Y H:i') ?? '-',
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Review export error: ' . $e->getMessage());
            return redirect()->route('admin.reviews.index')->with('error', 'Gagal mengekspor data ulasan.');
        }
    }

    /**
     * Print reviews analytics to PDF view
     */
    public function printAnalytics(Request $request)
    {
        try {
            $totalReviews = MongoReview::count();
            $ratingDistribution = [];

            foreach ([5, 4, 3, 2, 1] as $rating) {
                $count = MongoReview::where('rating', $rating)->count();
                $ratingDistribution[$rating] = [
                    'count' => $count,
                    'percentage' => $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0,
                ];
            }

            $sentimentSummary = [
                'total' => $totalReviews,
                'positive' => MongoReview::where('sentiment_label', 'positive')->count(),
                'neutral' => MongoReview::where('sentiment_label', 'neutral')->count(),
                'negative' => MongoReview::where('sentiment_label', 'negative')->count(),
                'pending' => MongoReview::whereNull('sentiment_label')->count(),
            ];

            $keywordSummary = [
                'overall' => [
                    'review_count' => 0,
                    'sentiment_counts' => ['negative' => 0, 'neutral' => 0, 'positive' => 0],
                    'top_keywords' => [],
                    'top_keywords_by_sentiment' => ['negative' => [], 'neutral' => [], 'positive' => []],
                ],
            ];

            $cacheKey = 'review_keyword_summary_' . date('Y-m-d_H'); // Cache per jam
            $cachedSummary = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHours(2), function () {
                $reviewsForKeywords = MongoReview::whereNotNull('sentiment_label')
                    ->orderBy('created_at', 'desc')
                    ->limit(500)
                    ->get(['_id', 'destination_id', 'review']);

                if ($reviewsForKeywords->isNotEmpty()) {
                    $summaryPayload = $reviewsForKeywords->map(fn ($review) => [
                        'id' => (string) $review->_id,
                        'text' => (string) ($review->review ?? ''),
                        'destination_id' => $review->destination_id ? (string) $review->destination_id : null,
                    ])->values()->all();

                    return $this->sentimentService->summaryKeywords($summaryPayload, 50);
                }
                return null;
            });

            if ($cachedSummary && ($cachedSummary['success'] ?? false)) {
                $summaryData = $cachedSummary['data'] ?? [];
                $keywordSummary = $this->enrichKeywordSummaryWithDestinationNames($summaryData['keyword_summary'] ?? $keywordSummary);
            }

            $instansi = $request->input('instansi', 'PEMERINTAH KABUPATEN TOBA/DINAS KEBUDAYAAN DAN PARIWISATA');
            $alamat = $request->input('alamat', 'Jl. Bukit Pagar Batu No. 1, Balige, Kabupaten Toba, Sumatera Utara');
            $email = $request->input('email', 'disbudpar@tobakab.go.id');
            $telp = $request->input('telp', '(0632) 123456');
            $website = $request->input('website', 'https://disbudpar.tobakab.go.id');
            $nomorSurat = $request->input('nomor_surat', '050/322/Disbudpar/' . date('Y'));
            $hal = $request->input('hal', 'Laporan Analitik Ulasan Pengguna');
            $namaPenandatangan = $request->input('nama_penandatangan', 'Sandro M. S. Simanjuntak, S.T., M.Si.');
            $nipPenandatangan = $request->input('nip_penandatangan', '19780512 200501 1 003');
            $jabatan = $request->input('jabatan', 'Kepala Dinas Kebudayaan dan Pariwisata');
            
            $logoUrl = null;
            if ($request->hasFile('custom_logo')) {
                $file = $request->file('custom_logo');
                $extension = $file->getClientOriginalExtension();
                $logoUrl = 'data:image/' . ($extension == 'svg' ? 'svg+xml' : $extension) . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            } else {
                $logoPath = \App\Models\AppSetting::get('logo');
                $logoUrl = $logoPath ? (str_starts_with($logoPath, 'http') ? $logoPath : \Illuminate\Support\Facades\Storage::url($logoPath)) : null;
            }

            return view('admin.reviews.print_analytics', compact(
                'ratingDistribution',
                'sentimentSummary',
                'keywordSummary',
                'instansi',
                'alamat',
                'email',
                'telp',
                'website',
                'nomorSurat',
                'hal',
                'namaPenandatangan',
                'nipPenandatangan',
                'jabatan',
                'logoUrl'
            ));
        } catch (\Exception $e) {
            Log::error('Error generating print analytics: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data analitik ulasan untuk dicetak.');
        }
    }

    /**
     * Normalize prediction payload from single/batch API variants.
     */
    private function normalizePrediction(array $prediction): array
    {
        $label = (string) ($prediction['sentiment_label'] ?? $prediction['label'] ?? 'neutral');
        $scores = $prediction['sentiment_scores'] ?? $prediction['scores'] ?? [];
        if (!is_array($scores)) {
            $scores = [];
        }

        $confidence = (float) ($prediction['sentiment_confidence'] ?? $prediction['confidence'] ?? 0);
        if (isset($scores[$label]) && is_numeric($scores[$label])) {
            // Keep confidence aligned with final label score from Python output.
            $confidence = (float) $scores[$label];
        }

        return [
            'label' => $label,
            'confidence' => $confidence,
            'scores' => $scores,
            'reason' => (string) ($prediction['sentiment_reason'] ?? $prediction['reason'] ?? 'model_only'),
            'model_version' => $prediction['sentiment_model_version'] ?? $prediction['model_version'] ?? null,
        ];
    }

    /**
     * Ensure required score keys exist and confidence is consistent.
     */
    private function passesQualityGate(array $prediction): bool
    {
        $label = $prediction['label'] ?? null;
        $scores = $prediction['scores'] ?? [];
        $confidence = (float) ($prediction['confidence'] ?? 0);

        if (empty($label) || !is_array($scores)) {
            return false;
        }

        if (!isset($scores[$label]) || !is_numeric($scores[$label])) {
            return false;
        }

        $labelScore = (float) $scores[$label];
        return abs($confidence - $labelScore) <= 0.0001;
    }

    /**
     * Enrich keyword summary with destination names from MongoDB
     */
    private function enrichKeywordSummaryWithDestinationNames(array $keywordSummary): array
    {
        if (!empty($keywordSummary['destinations']) && is_array($keywordSummary['destinations'])) {
            $destIds = array_filter(array_map(fn($d) => $d['destination_id'] ?? null, $keywordSummary['destinations']));
            if (!empty($destIds)) {
                try {
                    $destNames = \App\Models\MongoDB\MongoDestination::whereIn('_id', $destIds)
                        ->get(['_id', 'name'])
                        ->pluck('name', '_id')
                        ->toArray();

                    foreach ($keywordSummary['destinations'] as $key => $dest) {
                        $id = $dest['destination_id'] ?? '';
                        $name = $destNames[$id] ?? 'Destinasi Tidak Dikenal';
                        $keywordSummary['destinations'][$key]['destination_name'] = $name;
                        $keywordSummary['destinations'][$key]['name'] = $name;
                    }
                } catch (\Exception $e) {
                    Log::error('Error enriching keyword summary destinations: ' . $e->getMessage());
                }
            }
        }
        return $keywordSummary;
    }

    /**
     * Clear review statistics and keyword summary caches
     */
    private function clearReviewCaches(): void
    {
        try {
            \Illuminate\Support\Facades\Cache::forget('review_stats_summary');
            \Illuminate\Support\Facades\Cache::forget('review_keyword_summary_' . date('Y-m-d_H'));
            \Illuminate\Support\Facades\Cache::forget('review_keyword_summary_' . date('Y-m-d_H', strtotime('-1 hour')));
        } catch (\Exception $e) {
            Log::error('Failed to clear review caches: ' . $e->getMessage());
        }
    }
}
