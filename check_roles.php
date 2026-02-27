<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking Admin roles...\n";
try {
    $admins = DB::table('admins')
        ->join('roles', 'admins.role_id', '=', 'roles.id')
        ->select('admins.name', 'admins.email', 'roles.name as role_name')
        ->get();
        
    foreach ($admins as $admin) {
        echo "- " . $admin->name . " (" . $admin->email . ") | Role: " . $admin->role_name . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
