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
            $query = MongoReview::with('destination');

            // Filter by rating
            if ($request->filled('rating')) {
                $query->where('rating', (int)$request->rating);
            }

            // Search in review text
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('review', 'like', "%{$search}%");
            }

            $reviews = $query->orderBy('created_at', 'desc')
                ->paginate(15);

            $ratings = [1, 2, 3, 4, 5];
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

                $summaryResponse = $this->sentimentService->summaryKeywords($summaryPayload, 50);

                if ($summaryResponse['success'] ?? false) {
                    $summaryData = $summaryResponse['data'] ?? [];
                    $keywordSummary = $summaryData['keyword_summary'] ?? $keywordSummary;
                    $predictionSummary = $summaryData['prediction_summary'] ?? [];
                    $keywordModelVersion = $summaryData['model_version'] ?? null;
                }
            }

            return view('admin.reviews.index', [
                'reviews' => $reviews,
                'ratings' => $ratings,
                'sentimentSummary' => $sentimentSummary,
                'ratingDistribution' => $ratingDistribution,
                'keywordSummary' => $keywordSummary,
                'predictionSummary' => $predictionSummary,
                'keywordModelVersion' => $keywordModelVersion,
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
            $review = MongoReview::with('destination')->findOrFail($id);

            // Handle AJAX/JSON requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($review);
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
}
