<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reports = iterator_to_array(\App\Models\MongoDB\MongoReport::getRawReports());
if(count($reports) > 0) {
    print_r($reports[0]);
} else {
    echo "No reports found\n";
}
