<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Services\SentimentAnalysisService;
use App\Models\MongoDB\MongoReview;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Review Sentiment Analysis Controller
 * Handles sentiment analysis for customer reviews
 */
class ReviewSentimentController extends BaseAdminController
{
    public function __construct(
        private SentimentAnalysisService $sentimentService
    ) {}
    
    /**
     * Analyze a single review
     */
    public function analyzeSingle(string $reviewId, Request $request): JsonResponse
    {
        try {
            $review = MongoReview::findOrFail($reviewId);
            
            if (!$this->sentimentService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sentiment service is currently unavailable',
                    'code' => 'SERVICE_UNAVAILABLE'
                ], 503);
            }
            
            // Get sentiment prediction
            $result = $this->sentimentService->predict(
                text: $review->review,
                reviewId: $review->_id
            );
            
            if ($result['success']) {
                // Store sentiment data
                $review->update([
                    'sentiment_label' => $result['label'],
                    'sentiment_confidence' => $result['confidence'],
                    'sentiment_scores' => $result['scores'],
                    'sentiment_reason' => $result['reason'],
                    'sentiment_analyzed_at' => now(),
                ]);
                
                $this->logActivity(
                    'analyze_review_sentiment',
                    'review',
                    $reviewId,
                    null,
                    [
                        'message' => "Analyzed sentiment: {$result['label']} (confidence: {$result['confidence']})"
                    ]
                );
            }
            
            return response()->json([
                'success' => $result['success'],
                'data' => $result,
                'review_id' => $reviewId
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Analyze all pending reviews (batch)
     */
    public function analyzeBatch(Request $request): JsonResponse
    {
        try {
            $limit = $request->integer('limit', 50);
            $limit = min($limit, 200); // Cap at 200 for performance
            
            if (!$this->sentimentService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sentiment service is currently unavailable'
                ], 503);
            }
            
            // Get reviews that haven't been analyzed
            $reviews = MongoReview::where('sentiment_label', null)
                ->limit($limit)
                ->get();
            
            if ($reviews->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pending reviews',
                    'total' => 0,
                    'analyzed' => 0
                ]);
            }
            
            // Prepare batch data
            $reviewData = $reviews->map(function ($review) {
                return [
                    'id' => (string)$review->_id,
                    'text' => $review->review
                ];
            })->toArray();
            
            // Call batch prediction
            $results = $this->sentimentService->predictBatch($reviewData);
            
            $analyzed = 0;
            if ($results['success']) {
                // Batch update reviews
                foreach ($results['data'] as $prediction) {
                    if ($prediction['success'] ?? false) {
                        MongoReview::where('_id', $prediction['id'])->update([
                            'sentiment_label' => $prediction['label'],
                            'sentiment_confidence' => $prediction['confidence'],
                            'sentiment_scores' => $prediction['scores'],
                            'sentiment_reason' => $prediction['reason'],
                            'sentiment_analyzed_at' => now(),
                        ]);
                        $analyzed++;
                    }
                }
            }
            
            $this->logActivity(
                'batch_analyze_reviews',
                'review',
                null,
                null,
                [
                    'message' => "Batch analyzed {$analyzed} reviews"
                ]
            );
            
            return response()->json([
                'success' => $results['success'],
                'message' => "Analyzed {$analyzed} out of {$reviews->count()} reviews",
                'total' => $reviews->count(),
                'analyzed' => $analyzed,
                'details' => $results['data'] ?? []
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Analyze all reviews for a specific destination
     */
    public function analyzeDestinationReviews(string $destinationId): JsonResponse
    {
        try {
            if (!$this->sentimentService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sentiment service unavailable'
                ], 503);
            }
            
            $reviews = MongoReview::where('destination_id', $destinationId)->get();
            
            if ($reviews->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No reviews found for destination'
                ]);
            }
            
            $reviewData = $reviews->map(function ($review) {
                return [
                    'id' => (string)$review->_id,
                    'text' => $review->review
                ];
            })->toArray();
            
            $results = $this->sentimentService->predictBatch($reviewData);
            
            $analyzed = 0;
            $sentiments = ['positive' => 0, 'neutral' => 0, 'negative' => 0];
            
            if ($results['success']) {
                foreach ($results['data'] as $prediction) {
                    if ($prediction['success'] ?? false) {
                        MongoReview::where('_id', $prediction['id'])->update([
                            'sentiment_label' => $prediction['label'],
                            'sentiment_confidence' => $prediction['confidence'],
                            'sentiment_scores' => $prediction['scores'],
                            'sentiment_reason' => $prediction['reason'],
                            'sentiment_analyzed_at' => now(),
                        ]);
                        $analyzed++;
                        $sentiments[$prediction['label']]++;
                    }
                }
            }
            
            return response()->json([
                'success' => $results['success'],
                'destination_id' => $destinationId,
                'total_reviews' => $reviews->count(),
                'analyzed' => $analyzed,
                'sentiment_summary' => $sentiments
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get sentiment statistics for dashboard
     */
    public function getSentimentStats(): JsonResponse
    {
        try {
            $total = MongoReview::count();
            $analyzed = MongoReview::whereNotNull('sentiment_label')->count();
            $pending = $total - $analyzed;
            
            $sentiments = [
                'positive' => MongoReview::where('sentiment_label', 'positive')->count(),
                'neutral' => MongoReview::where('sentiment_label', 'neutral')->count(),
                'negative' => MongoReview::where('sentiment_label', 'negative')->count(),
            ];
            
            $avgConfidence = MongoReview::whereNotNull('sentiment_confidence')
                ->avg('sentiment_confidence');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_reviews' => $total,
                    'analyzed' => $analyzed,
                    'pending' => $pending,
                    'analysis_rate' => $total > 0 ? round(($analyzed / $total) * 100, 2) : 0,
                    'sentiments' => $sentiments,
                    'sentiment_percentage' => [
                        'positive' => $total > 0 ? round(($sentiments['positive'] / $total) * 100, 2) : 0,
                        'neutral' => $total > 0 ? round(($sentiments['neutral'] / $total) * 100, 2) : 0,
                        'negative' => $total > 0 ? round(($sentiments['negative'] / $total) * 100, 2) : 0,
                    ],
                    'average_confidence' => $avgConfidence ? round($avgConfidence, 4) : null,
                ]
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check service health
     */
    public function checkServiceHealth(): JsonResponse
    {
        $isAvailable = $this->sentimentService->isAvailable();
        $stats = $isAvailable ? $this->sentimentService->getStats() : null;
        
        return response()->json([
            'success' => true,
            'service_available' => $isAvailable,
            'service_info' => $stats['data'] ?? null
        ]);
    }
}
