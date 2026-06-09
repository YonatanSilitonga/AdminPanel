<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$reviews = App\Models\MongoDB\MongoReview::all();
$destIds = [];
$userIds = [];

foreach ($reviews as $r) {
    if ($r->destination_id) $destIds[$r->destination_id] = true;
    if ($r->user_id) $userIds[$r->user_id] = true;
}

echo "Unique Destination IDs used in reviews:\n";
foreach (array_keys($destIds) as $id) {
    echo "- " . $id . "\n";
}

echo "\nUnique User IDs used in reviews:\n";
foreach (array_keys($userIds) as $id) {
    echo "- " . $id . "\n";
}
