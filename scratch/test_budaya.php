<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MongoDB\MongoBudaya;

$budayas = MongoBudaya::all();
echo "Total Budaya: " . $budayas->count() . "\n";
$controller = new \App\Http\Controllers\Admin\BudayaController();
try {
    foreach (\App\Models\MongoDB\MongoDestination::all() as $d) {
        $x = $d->facilities;
    }
    echo "Checked all destinations.\n";

    foreach (\App\Models\MongoDB\MongoEvent::all() as $e) {
        $x = $e->images;
        $y = $e->tags;
        $z = $e->schedule;
    }
    echo "Checked all events.\n";

    foreach (\App\Models\MongoDB\MongoFasilitasUmum::all() as $f) {
        $x = $f->images;
        $y = $f->tags;
        $z = $f->available_services;
    }
    echo "Checked all fasilitas umum.\n";

    foreach (\App\Models\MongoDB\MongoBeritaPromosi::all() as $b) {
        $x = $b->images;
        $y = $b->videos;
    }
    echo "Checked all berita promosi.\n";
} catch (\Throwable $ex) {
    echo "Exception occurred: " . $ex->getMessage() . "\n";
    echo "Trace: " . $ex->getTraceAsString() . "\n";
}




