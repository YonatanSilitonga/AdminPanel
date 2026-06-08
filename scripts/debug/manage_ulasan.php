<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MongoDB\MongoReview;

echo "--- 1. Resetting existing sentiment data ---\n";
$resetCount = MongoReview::query()->update([
    'sentiment_label' => null,
    'sentiment_confidence' => null,
    'sentiment_scores' => null,
    'sentiment_reason' => null,
    'sentiment_model_version' => null,
    'sentiment_analyzed_at' => null,
]);
echo "Successfully reset {$resetCount} reviews to Pending state.\n\n";

echo "--- 2. Adding new test reviews ---\n";
$newReviews = [
    [
        'destination_id' => '69fac5e2fbb354beb804a9fb',
        'user_id' => 'Budi Siregar',
        'rating' => 5,
        'review' => 'Pemandangan air terjun ini luar biasa indah! Sangat menakjubkan melihat airnya jatuh langsung ke Danau Toba. Pelayanan kapal juga sangat baik.',
        'status' => 'active'
    ],
    [
        'destination_id' => '69fab779fbb354beb804a9e4',
        'user_id' => 'Dewi Gultom',
        'rating' => 1,
        'review' => 'Jalanan menuju ke bukit ini sangat rusak parah dan sempit. Tidak ada penerangan jalan, sangat membahayakan bagi pengendara. Kecewa sekali.',
        'status' => 'active'
    ],
    [
        'destination_id' => '69fabe02fbb354beb804a9ee',
        'user_id' => 'Andrian Simanjuntak',
        'rating' => 3,
        'review' => 'Pantainya lumayan bersih, tapi fasilitas toilet dan tempat bilasnya kurang terawat. Harganya juga standar saja.',
        'status' => 'active'
    ],
    [
        'destination_id' => '69faed36fb3cb925ad0bdaec',
        'user_id' => 'Maria Situmorang',
        'rating' => 2,
        'review' => 'Tempatnya sangat becek, bau, dan semrawut sekali penataan pedagangnya. Sangat tidak nyaman berbelanja di sini.',
        'status' => 'active'
    ],
    [
        'destination_id' => '69fac159fbb354beb804a9f3',
        'user_id' => 'Ferry Nababan',
        'rating' => 4,
        'review' => 'Tempatnya bersih dan dekat dengan pelabuhan. Sangat cocok untuk bersantai sejenak menunggu penyeberangan kapal.',
        'status' => 'active'
    ],
    [
        'destination_id' => '69fae660fb3cb925ad0bdae2',
        'user_id' => 'Santi Marpaung',
        'rating' => 5,
        'review' => 'Destinasi wisata yang sangat berkesan dan seru! Pemandangan alamnya luar biasa indah dan udaranya segar sekali. Sangat direkomendasikan!',
        'status' => 'active'
    ]
];

$insertedCount = 0;
foreach ($newReviews as $reviewData) {
    // Check if review already exists to prevent duplicate runs
    $exists = MongoReview::where('user_id', $reviewData['user_id'])
                         ->where('review', $reviewData['review'])
                         ->exists();
    if (!$exists) {
        MongoReview::create($reviewData);
        $insertedCount++;
    }
}

echo "Successfully added {$insertedCount} new test reviews to MongoDB.\n";
echo "Total reviews now: " . MongoReview::count() . "\n";
echo "Pending reviews now: " . MongoReview::whereNull('sentiment_label')->count() . "\n";
