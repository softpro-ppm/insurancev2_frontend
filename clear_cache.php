<?php
/**
 * Cache Clear Script for Hostinger
 * Upload this file to your Laravel project root and visit it in browser
 * Then delete this file after use
 */

echo "<h2>Clearing Laravel Cache...</h2>";

// Change to your Laravel project directory
$laravelPath = __DIR__;

// Commands to run
$commands = [
    'route:clear',
    'config:clear', 
    'cache:clear',
    'view:clear'
];

foreach ($commands as $command) {
    echo "<p>Running: php artisan {$command}</p>";
    $output = [];
    $returnCode = 0;
    exec("cd {$laravelPath} && php artisan {$command} 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<span style='color: green;'>✓ Success</span><br>";
        if (!empty($output)) {
            echo "<small>" . implode('<br>', $output) . "</small><br>";
        }
    } else {
        echo "<span style='color: red;'>✗ Failed</span><br>";
        if (!empty($output)) {
            echo "<small style='color: red;'>" . implode('<br>', $output) . "</small><br>";
        }
    }
    echo "<hr>";
}

echo "<h3>Cache clearing complete!</h3>";
echo "<p><strong>Important:</strong> Delete this file (clear_cache.php) after use for security.</p>";
echo "<p>Now try the document removal functionality again.</p>";
?>
