<?php

use App\Models\MongoDB\MongoDestination;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Dumping first destination...\n";
try {
    $dest = MongoDestination::first();
    if ($dest) {
        echo json_encode($dest->toArray(), JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "No destinations found.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
