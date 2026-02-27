<?php

use App\Models\MongoDB\MongoDestination;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking MongoDB destinations...\n";
try {
    $all = MongoDestination::all();
    echo "Total Destinations: " . $all->count() . "\n";
    
    foreach ($all as $dest) {
        echo "- " . $dest->name . " (ID: " . $dest->_id . ") | Created: " . $dest->created_at . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
