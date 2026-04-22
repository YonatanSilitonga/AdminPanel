<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$reviews = App\Models\MongoDB\MongoReview::whereNull('sentiment_label')
    ->limit(5)
    ->get()
    ->map(fn ($r) => [
        'id' => (string) $r->_id,
        'text' => $r->review ?? '',
    ])
    ->values()
    ->all();

$svc = app(App\Services\SentimentAnalysisService::class);
$res = $svc->predictBatch($reviews);

print_r($res);
