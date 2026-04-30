<?php

use App\Models\MongoDB\MongoReport;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking MongoDB reports...\n";
try {
    $all = MongoReport::all();
    echo "Total Reports: " . $all->count() . "\n";
    
    foreach ($all as $report) {
        echo "ID: " . $report->_id . " | Status: " . $report->status . " | Description: " . $report->description . " | Reason: " . ($report->reason ?? 'N/A') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
