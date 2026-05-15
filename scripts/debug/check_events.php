<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MongoDB\MongoEvent;
use Illuminate\Support\Facades\DB;

echo "=== Final Verification ===" . PHP_EOL;

$mongo = DB::connection('mongodb')->getMongoDB();

echo "New 'events' collection count: " . $mongo->selectCollection('events')->countDocuments() . PHP_EOL;
echo "New 'destinations' collection count: " . $mongo->selectCollection('destinations')->countDocuments() . PHP_EOL;
echo PHP_EOL;

echo "Sample Event from 'events' collection:" . PHP_EOL;
$event = $mongo->selectCollection('events')->findOne();
if ($event) {
    echo "  - name: " . $event['name'] . PHP_EOL;
    echo "  - is_active: " . var_export($event['is_active'], true) . " (" . gettype($event['is_active']) . ")" . PHP_EOL;
    echo "  - banner_url: " . ($event['banner_url'] ?? 'N/A') . PHP_EOL;
} else {
    echo "  No events found in 'events' collection." . PHP_EOL;
}
echo PHP_EOL;

echo "Sample Destination from 'destinations' collection:" . PHP_EOL;
$dest = $mongo->selectCollection('destinations')->findOne();
if ($dest) {
    echo "  - name: " . $dest['name'] . PHP_EOL;
    echo "  - is_active: " . var_export($dest['is_active'], true) . " (" . gettype($dest['is_active']) . ")" . PHP_EOL;
} else {
    echo "  No destinations found in 'destinations' collection." . PHP_EOL;
}
echo PHP_EOL;

echo "Model Check (MongoEvent):" . PHP_EOL;
$model = new MongoEvent();
echo "  - Collection: " . $model->getTable() . PHP_EOL;
$latest = MongoEvent::orderBy('created_at', 'desc')->first();
if ($latest) {
    echo "  - Latest Event Model Name: " . $latest->name . PHP_EOL;
} else {
    echo "  - Model couldn't find any events in the collection." . PHP_EOL;
}
