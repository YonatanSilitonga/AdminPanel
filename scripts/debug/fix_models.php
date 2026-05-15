<?php

$dir = 'app/Models/MongoDB/';
$files = scandir($dir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $path = $dir . $file;
    $content = file_get_contents($path);
    
    if (strpos($content, 'protected $collection') !== false) {
        echo "Fixing $file...\n";
        // If it doesn't already have $table, change $collection to $table
        if (strpos($content, 'protected $table') === false) {
            $content = str_replace('protected $collection', 'protected $table', $content);
        } else {
            // If it has both, we can leave it or remove $collection. Let's just ensure $table is correct.
            // In MongoDestination it has both.
        }
        file_put_contents($path, $content);
    }
}
