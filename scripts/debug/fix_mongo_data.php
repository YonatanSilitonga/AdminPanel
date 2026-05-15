<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$mongo = DB::connection('mongodb')->getMongoDB();

echo "=== Data Migration & Fix Script ===" . PHP_EOL;

// 1. Move events from mongo_events to events
$oldEvents = $mongo->selectCollection('mongo_events')->find([]);
$countEvents = 0;
foreach ($oldEvents as $doc) {
    // Convert to array and handle types
    $data = (array)$doc;
    $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    
    // Insert into 'events'
    $mongo->selectCollection('events')->replaceOne(['_id' => $doc['_id']], $data, ['upsert' => true]);
    $countEvents++;
}
echo "Migrated $countEvents events to 'events' collection." . PHP_EOL;

// 2. Move destinations from mongo_destinations to destinations
$oldDestinations = $mongo->selectCollection('mongo_destinations')->find([]);
$countDestinations = 0;
foreach ($oldDestinations as $doc) {
    $data = (array)$doc;
    $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $data['latitude'] = (float)($data['latitude'] ?? 0);
    $data['longitude'] = (float)($data['longitude'] ?? 0);
    $data['average_rating'] = (float)($data['average_rating'] ?? 0);
    $data['total_reviews'] = (int)($data['total_reviews'] ?? 0);

    $mongo->selectCollection('destinations')->replaceOne(['_id' => $doc['_id']], $data, ['upsert' => true]);
    $countDestinations++;
}
echo "Migrated $countDestinations destinations to 'destinations' collection." . PHP_EOL;

// 3. Optional: drop old collections
if ($countEvents > 0) {
    // $mongo->selectCollection('mongo_events')->drop();
    echo "Old 'mongo_events' collection still exists for safety." . PHP_EOL;
}
if ($countDestinations > 0) {
    // $mongo->selectCollection('mongo_destinations')->drop();
    echo "Old 'mongo_destinations' collection still exists for safety." . PHP_EOL;
}

echo PHP_EOL . "Done!" . PHP_EOL;
