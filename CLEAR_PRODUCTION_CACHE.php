<?php

/**
 * Clear Production Cache Script
 * 
 * Upload this file to your production server and run it to clear all caches
 */

echo "=== Clearing Production Cache ===\n\n";

// Clear Laravel caches
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear', 
    'php artisan route:clear',
    'php artisan view:clear',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    echo "Running: {$command}\n";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "Output: " . trim($output) . "\n";
    }
    echo "✅ Completed\n\n";
}

// Clear file system caches
$cacheDirectories = [
    'storage/framework/cache/',
    'storage/framework/views/',
    'storage/framework/sessions/',
    'bootstrap/cache/'
];

foreach ($cacheDirectories as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                echo "Deleted: {$file}\n";
            }
        }
        echo "✅ Cleared cache directory: {$dir}\n";
    }
}

echo "\n=== Cache Clearing Complete ===\n";
echo "All caches have been cleared. Try accessing your website now.\n";
