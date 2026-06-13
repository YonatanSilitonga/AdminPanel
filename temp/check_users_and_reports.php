<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$mongo = DB::connection('mongodb')->getMongoDB();

$usersCount = $mongo->selectCollection('users')->countDocuments();
echo "USERS COUNT: $usersCount\n";
if ($usersCount > 0) {
    echo "USERS SAMPLE:\n";
    $users = $mongo->selectCollection('users')->find([], ['limit' => 10]);
    foreach ($users as $u) {
        echo json_encode($u) . "\n";
    }
}

$reportsCount = $mongo->selectCollection('reports')->countDocuments();
echo "REPORTS COUNT: $reportsCount\n";
if ($reportsCount > 0) {
    echo "REPORTS SAMPLE:\n";
    $reports = $mongo->selectCollection('reports')->find([], ['limit' => 5]);
    foreach ($reports as $r) {
        echo json_encode($r) . "\n";
    }
}
