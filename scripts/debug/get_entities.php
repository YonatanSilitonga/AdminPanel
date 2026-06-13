<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- DESTINATIONS ---\n";
$dests = App\Models\MongoDB\MongoDestination::limit(5)->get();
foreach ($dests as $d) {
    echo "ID: " . $d->_id . " | Name: " . $d->name . "\n";
}

echo "\n--- USERS ---\n";
$users = App\Models\User::limit(5)->get();
foreach ($users as $u) {
    echo "ID: " . $u->_id . " | Name: " . $u->name . "\n";
}
