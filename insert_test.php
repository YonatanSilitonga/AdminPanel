<?php

use App\Models\MongoDB\MongoDestination;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Inserting test destination...\n";
try {
    $dest = new MongoDestination();
    $dest->name = "Test Destination " . time();
    $dest->description = "This is a test created from CLI";
    $dest->location = "CLI Location";
    $dest->category = "nature";
    $dest->latitude = 1.0;
    $dest->longitude = 100.0;
    $dest->average_rating = 5.0;
    $dest->total_reviews = 10;
    $dest->is_active = true;
    $dest->is_featured = false;
    $dest->images = [];
    $dest->save();
    
    echo "Inserted successfully! ID: " . $dest->_id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
