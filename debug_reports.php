<?php

use App\Models\MongoDB\MongoReport;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Dumping first 3 reports...\n";
try {
    $reports = MongoReport::take(3)->get();
    foreach ($reports as $report) {
        echo "Report ID: " . $report->_id . "\n";
        echo json_encode($report->toArray(), JSON_PRETTY_PRINT) . "\n";
        echo "All Image URLs (appended): " . json_encode($report->all_image_urls, JSON_PRETTY_PRINT) . "\n";
        echo "-----------------------------------\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
