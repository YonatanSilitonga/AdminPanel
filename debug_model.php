<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MongoDB\MongoReport;

$model = new MongoReport();
echo "Model Connection: " . $model->getConnectionName() . "\n";
echo "Model Table/Collection: " . $model->getTable() . "\n";
echo "Model Class: " . get_class($model) . "\n";
echo "Parent Class: " . get_parent_class($model) . "\n";
echo "Count: " . MongoReport::count() . "\n";
