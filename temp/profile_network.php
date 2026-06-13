<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DIAGNOSING MONGODB CONNECTION TIMINGS ===\n";

$dsn = env('MONGODB_DSN');
echo "DSN: " . preg_replace('/:[^@:]+@/', ':***@', $dsn) . "\n\n";

// Parse host from DSN
$host = "cluster0.9gwjswd.mongodb.net";
if (preg_match('/@([^\/]+)/', $dsn, $matches)) {
    $host = explode('?', $matches[1])[0];
}
echo "Parsed target host: $host\n";

// 1. Test DNS Resolution
$start = microtime(true);
$ips = gethostbynamel($host);
$dnsTime = microtime(true) - $start;
echo "1. DNS Resolution for $host: " . ($ips ? implode(', ', $ips) : 'FAILED') . " (took " . round($dnsTime * 1000, 2) . "ms)\n";

// 2. Resolve SRV record
$start = microtime(true);
$srvRecords = dns_get_record("_mongodb._tcp." . $host, DNS_SRV);
$srvTime = microtime(true) - $start;
echo "2. DNS SRV Lookup: " . ($srvRecords ? count($srvRecords) . " records found" : 'FAILED') . " (took " . round($srvTime * 1000, 2) . "ms)\n";

if ($srvRecords) {
    foreach ($srvRecords as $index => $srv) {
        $target = $srv['target'];
        $port = $srv['port'];
        
        $startConnect = microtime(true);
        $fp = @fsockopen($target, $port, $errno, $errstr, 2.0); // 2 second timeout
        $connectTime = microtime(true) - $startConnect;
        
        if ($fp) {
            echo "   - TCP connection to $target:$port: SUCCESS (took " . round($connectTime * 1000, 2) . "ms)\n";
            fclose($fp);
        } else {
            echo "   - TCP connection to $target:$port: FAILED ($errstr) (took " . round($connectTime * 1000, 2) . "ms)\n";
        }
    }
}

// 3. Test MongoDB client connection and ping command
echo "\n3. Testing MongoDB Client Connection & Ping:\n";
$startClient = microtime(true);
try {
    $db = DB::connection('mongodb')->getMongoDB();
    $instantiateTime = microtime(true) - $startClient;
    echo "   - Client instantiated in " . round($instantiateTime * 1000, 2) . "ms\n";
    
    $startCommand = microtime(true);
    $result = DB::connection('mongodb')->getMongoDB()->command(['ping' => 1])->toArray();
    $commandTime = microtime(true) - $startCommand;
    echo "   - Ping command: SUCCESS " . json_encode($result) . " (took " . round($commandTime * 1000, 2) . "ms)\n";
} catch (\Exception $e) {
    $errorTime = microtime(true) - $startClient;
    echo "   - Connection/Ping FAILED: " . $e->getMessage() . " (took " . round($errorTime * 1000, 2) . "ms)\n";
}

echo "\n4. Running a count query on 'events':\n";
$startQuery = microtime(true);
try {
    $count = DB::connection('mongodb')->getCollection('events')->countDocuments();
    echo "   - Count events: $count (took " . round((microtime(true) - $startQuery) * 1000, 2) . "ms)\n";
} catch (\Exception $e) {
    echo "   - Count query FAILED: " . $e->getMessage() . " (took " . round((microtime(true) - $startQuery) * 1000, 2) . "ms)\n";
}
