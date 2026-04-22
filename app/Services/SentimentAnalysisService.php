<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Sentiment Analysis Service
 * Communicates with Python Flask API for sentiment classification
 * 
 * Usage:
 * $service = app(SentimentAnalysisService::class);
 * $result = $service->predict('Review text here', 123);
 */
class SentimentAnalysisService
{
    private const MAX_LOG_TEXT_LENGTH = 200;

    /**
     * Python API base URL
     */
    protected string $apiUrl;
    
    /**
     * Request timeout in seconds
     */
    protected int $timeout = 30;

    /**
     * Batch request timeout in seconds
     */
    protected int $batchTimeout = 90;
    
    /**
     * Enable/disable service
     */
    protected bool $enabled;
    
    public function __construct()
    {
        $this->apiUrl = config('services.sentiment.url', 'http://127.0.0.1:5000');
        $this->timeout = (int) config('services.sentiment.timeout', 30);
        $this->batchTimeout = (int) config('services.sentiment.batch_timeout', 90);
        $this->enabled = config('services.sentiment.enabled', true);
    }
    
    /**
     * Check if sentiment service is available
     */
    public function isAvailable(): bool
    {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            $response = $this->httpClient($this->timeout)->get("{$this->apiUrl}/health");
            if (!$response->successful()) {
                return false;
            }

            $data = $response->json();
            return ($data['status'] ?? null) === 'healthy' || ($data['success'] ?? false) === true;
        } catch (Exception $e) {
            Log::warning('Sentiment service health check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Predict sentiment for a single review
     * 
     * @param string $text Review text
     * @param int|null $reviewId Optional review ID for tracking
     * @return array Prediction result
     */
    public function predict(string $text, string|int|null $reviewId = null): array
    {
        if (!$this->enabled) {
            Log::info('Sentiment service is disabled');
            return $this->getDisabledResponse();
        }
        
        if (empty(trim($text))) {
            return [
                'success' => false,
                'error' => 'Empty text provided',
                'label' => null,
                'confidence' => null
            ];
        }
        
        try {
            $endpoint = "{$this->apiUrl}/api/v1/predict";
            $payload = [
                'text' => trim($text),
                'review_id' => $reviewId,
                'model_version' => 'v9'
            ];

            $this->logRequest('predict', $endpoint, $payload);

            $response = $this->httpClient($this->timeout)->post($endpoint, $payload);
            
            if (!$response->successful()) {
                $this->logFailedResponse('predict', $response->status(), $response->body());
                Log::warning(
                    'Sentiment prediction failed',
                    [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'review_id' => $reviewId
                    ]
                );
                return $this->getErrorResponse('API returned error status: ' . $response->status());
            }
            
            $data = $response->json();
            
            if (!($data['success'] ?? false)) {
                return $this->getErrorResponse($data['error'] ?? 'Unknown error');
            }
            
            // Extract and format response
            return [
                'success' => true,
                'label' => $data['data']['label'] ?? 'neutral',
                'confidence' => $data['data']['confidence'] ?? 0,
                'scores' => $data['data']['scores'] ?? [],
                'reason' => $data['data']['reason'] ?? 'unknown',
                'processed_text' => $data['data']['processed_text'] ?? '',
                'model_version' => $data['data']['model_version'] ?? ($data['data']['sentiment_model_version'] ?? null),
                'sentiment_model_version' => $data['data']['sentiment_model_version'] ?? ($data['data']['model_version'] ?? null),
                'sentence_predictions' => $data['data']['sentence_predictions'] ?? null,
                'timestamp' => $data['data']['timestamp'] ?? now()->toIso8601String()
            ];
        
        } catch (Exception $e) {
            Log::error(
                'Sentiment prediction exception',
                [
                    'error' => $e->getMessage(),
                    'review_id' => $reviewId
                ]
            );
            
            return $this->getErrorResponse('Service unavailable: ' . $e->getMessage());
        }
    }
    
    /**
     * Predict sentiment for multiple reviews (batch)
     * 
     * @param array $reviews Array of ['id' => int, 'text' => string]
     * @return array Batch prediction results
     */
    public function predictBatch(array $reviews): array
    {
        if (!$this->enabled) {
            Log::info('Sentiment service is disabled');
            return [
                'success' => false,
                'error' => 'Service disabled',
                'data' => []
            ];
        }
        
        $normalizedReviews = $this->normalizeReviews($reviews);
        
        if (empty($normalizedReviews)) {
            Log::warning('predict-batch skipped: no valid review items after normalization', [
                'input_count' => count($reviews),
            ]);

            return [
                'success' => false,
                'error' => 'No valid reviews to process',
                'data' => []
            ];
        }
        
        try {
            $endpoint = "{$this->apiUrl}/api/v1/predict-batch";
            $payload = [
                'reviews' => $normalizedReviews,
            ];

            $this->logRequest('predict-batch', $endpoint, $payload);

            $response = $this->httpClient($this->batchTimeout)->post($endpoint, $payload);
            
            if (!$response->successful()) {
                $this->logFailedResponse('predict-batch', $response->status(), $response->body());
                Log::warning('Batch prediction failed', ['status' => $response->status()]);
                return [
                    'success' => false,
                    'error' => 'API error',
                    'data' => []
                ];
            }
            
            $data = $response->json();

            if (!isset($data['data']['results']) || !is_array($data['data']['results'])) {
                Log::warning('Batch prediction response missing data.results', [
                    'keys' => array_keys((array) ($data['data'] ?? [])),
                ]);

                return [
                    'success' => false,
                    'error' => 'Invalid response: missing data.results',
                    'data' => []
                ];
            }
            
            if (!($data['success'] ?? false)) {
                return [
                    'success' => false,
                    'error' => $data['error'] ?? 'Unknown error',
                    'data' => []
                ];
            }
            
            return [
                'success' => true,
                'results' => $data['data']['results'],
                'data' => $data['data'] ?? [],
                'total' => $data['total'] ?? 0,
                'successful' => $data['successful'] ?? 0,
                'timestamp' => $data['timestamp'] ?? now()->toIso8601String()
            ];
        
        } catch (Exception $e) {
            Log::error('Batch prediction exception', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => 'Service unavailable: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Build keyword summary for dashboard/ringkasan page.
     *
     * @param array $reviews Array of ['id' => string, 'text' => string, 'destination_id' => string|null]
     */
    public function summaryKeywords(array $reviews, int $topN = 20): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'error' => 'Service disabled', 'data' => []];
        }

        $validReviews = $this->normalizeReviews($reviews);

        if (empty($validReviews)) {
            Log::info('summary-keywords skipped: no valid review items after normalization', [
                'input_count' => count($reviews),
            ]);
            return ['success' => true, 'data' => ['prediction_summary' => [], 'keyword_summary' => ['overall' => [], 'destinations' => []]]];
        }

        try {
            $endpoint = "{$this->apiUrl}/api/v1/summary-keywords";
            $payload = [
                'reviews' => $validReviews,
                'top_n' => max(1, $topN),
            ];

            $this->logRequest('summary-keywords', $endpoint, $payload);

            $response = $this->httpClient($this->batchTimeout)->post($endpoint, $payload);

            if (!$response->successful()) {
                $this->logFailedResponse('summary-keywords', $response->status(), $response->body());
                return [
                    'success' => false,
                    'error' => "API error {$response->status()}",
                    'data' => [],
                ];
            }

            $data = $response->json();
            if (!($data['success'] ?? false)) {
                return ['success' => false, 'error' => $data['error'] ?? 'Unknown error', 'data' => []];
            }

            if (!isset($data['data']['keyword_summary']) || !is_array($data['data']['keyword_summary'])) {
                Log::warning('Summary keywords response missing data.keyword_summary', [
                    'keys' => array_keys((array) ($data['data'] ?? [])),
                ]);

                return [
                    'success' => false,
                    'error' => 'Invalid response: missing data.keyword_summary',
                    'data' => [],
                ];
            }

            return ['success' => true, 'data' => $data['data'] ?? []];
        } catch (Exception $e) {
            Log::error('Keyword summary exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Service unavailable: ' . $e->getMessage(), 'data' => []];
        }
    }
    
    /**
     * Get sentiment statistics/info
     */
    public function getStats(): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'error' => 'Service disabled'];
        }
        
        try {
            $response = Http::timeout($this->timeout)->get("{$this->apiUrl}/api/v1/stats");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return ['success' => false, 'error' => 'Could not fetch stats'];
        
        } catch (Exception $e) {
            Log::error('Failed to get stats', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get error response structure
     */
    protected function getErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message,
            'label' => 'neutral',
            'confidence' => 0,
            'scores' => ['negative' => 0, 'neutral' => 1, 'positive' => 0],
            'reason' => 'error'
        ];
    }
    
    /**
     * Get disabled response
     */
    protected function getDisabledResponse(): array
    {
        return [
            'success' => false,
            'error' => 'Sentiment analysis service is disabled',
            'label' => null,
            'confidence' => null,
            'reason' => 'disabled'
        ];
    }

    /**
     * Build a consistent JSON client for Python API calls.
     */
    private function httpClient(int $timeout): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'laravel-adminpanel-sentiment-client/1.0',
            ])
            ->timeout($timeout);
    }

    /**
     * Normalize payload review items to Python contract keys.
     *
     * @return array<int, array{id: string, destination_id: string|null, text: string}>
     */
    private function normalizeReviews(array $reviews): array
    {
        $normalized = [];

        foreach ($reviews as $review) {
            if (!is_array($review)) {
                continue;
            }

            $text = (string) ($review['text'] ?? $review['review'] ?? $review['review_text'] ?? '');
            $text = trim($text);

            if ($text === '') {
                continue;
            }

            $id = (string) ($review['id'] ?? $review['review_id'] ?? '');
            $destinationId = $review['destination_id'] ?? null;

            $normalized[] = [
                'id' => $id,
                'destination_id' => $destinationId !== null ? (string) $destinationId : null,
                'text' => $text,
            ];
        }

        return $normalized;
    }

    private function logRequest(string $operation, string $endpoint, array $payload): void
    {
        $reviews = $payload['reviews'] ?? $payload['items'] ?? $payload['data'] ?? [];
        if (!is_array($reviews)) {
            $reviews = [];
        }

        Log::info('Sentiment API request', [
            'operation' => $operation,
            'url' => $endpoint,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'laravel-adminpanel-sentiment-client/1.0',
            ],
            'timeout_sec' => str_contains($operation, 'batch') || $operation === 'summary-keywords' ? $this->batchTimeout : $this->timeout,
            'retry_count' => 0,
            'reviews_count' => count($reviews),
            'first_item_sample' => $this->sanitizeReviewItemForLog($reviews[0] ?? null),
            'payload' => $this->sanitizePayloadForLog($payload),
        ]);
    }

    private function logFailedResponse(string $operation, int $status, string $body): void
    {
        Log::warning('Sentiment API failed response', [
            'operation' => $operation,
            'status' => $status,
            'body' => mb_substr($body, 0, 4000),
        ]);
    }

    private function sanitizePayloadForLog(array $payload): array
    {
        $copy = $payload;
        $reviewKeys = ['reviews', 'items', 'data'];

        foreach ($reviewKeys as $key) {
            if (!isset($copy[$key]) || !is_array($copy[$key])) {
                continue;
            }

            $copy[$key] = array_map(function ($item) {
                return $this->sanitizeReviewItemForLog($item);
            }, array_slice($copy[$key], 0, 5));
        }

        return $copy;
    }

    private function sanitizeReviewItemForLog(mixed $item): mixed
    {
        if (!is_array($item)) {
            return $item;
        }

        $safe = $item;
        if (isset($safe['text']) && is_string($safe['text'])) {
            $safe['text'] = mb_substr($safe['text'], 0, self::MAX_LOG_TEXT_LENGTH);
        }

        return $safe;
    }
}
