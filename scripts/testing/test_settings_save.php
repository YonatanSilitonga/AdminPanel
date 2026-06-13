<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AppSetting;

echo "--- Testing AppSetting operations ---\n";

try {
    // 1. Get original settings
    $original = AppSetting::getAllSettings();
    echo "Current settings count: " . count($original) . "\n";

    // 2. Perform test modifications
    echo "Updating settings...\n";
    
    // Set a few sample keys
    AppSetting::set('enable_reviews', true, 'boolean');
    AppSetting::set('enable_reports', false, 'boolean');
    AppSetting::set('primary_color', '#FF0000', 'string');
    AppSetting::set('secondary_color', '#00FF00', 'string');
    AppSetting::set('default_language', 'id', 'string');
    AppSetting::set('dark_mode', true, 'boolean');
    AppSetting::set('notify_new_review', true, 'boolean');
    AppSetting::set('notify_new_report', true, 'boolean');
    AppSetting::set('notify_new_user', false, 'boolean');
    AppSetting::set('notify_system_error', false, 'boolean');

    // 3. Verify they were updated and correctly cast
    $updated = AppSetting::getAllSettings();
    
    $tests = [
        'enable_reviews' => true,
        'enable_reports' => false,
        'primary_color' => '#FF0000',
        'secondary_color' => '#00FF00',
        'default_language' => 'id',
        'dark_mode' => true,
        'notify_new_review' => true,
        'notify_new_report' => true,
        'notify_new_user' => false,
        'notify_system_error' => false,
    ];

    $failed = 0;
    foreach ($tests as $key => $expectedValue) {
        $actualValue = AppSetting::get($key);
        $type = gettype($actualValue);
        
        if ($actualValue === $expectedValue) {
            echo "✔ Key '{$key}' set correctly to " . json_encode($actualValue) . " ({$type})\n";
        } else {
            echo "✘ Key '{$key}' failed! Expected: " . json_encode($expectedValue) . ", got: " . json_encode($actualValue) . " ({$type})\n";
            $failed++;
        }
    }

    // 4. Restore original settings to leave the system clean
    echo "Restoring original settings...\n";
    foreach ($original as $key => $val) {
        $type = is_bool($val) ? 'boolean' : (is_int($val) ? 'integer' : (is_float($val) ? 'float' : (is_array($val) ? 'json' : 'string')));
        AppSetting::set($key, $val, $type);
    }
    
    // Clean up any keys that didn't exist originally
    foreach ($tests as $key => $val) {
        if (!array_key_exists($key, $original)) {
            AppSetting::remove($key);
        }
    }

    if ($failed === 0) {
        echo "\nSUCCESS: All AppSetting test operations completed successfully!\n";
        exit(0);
    } else {
        echo "\nFAILURE: {$failed} settings verification checks failed.\n";
        exit(1);
    }

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
