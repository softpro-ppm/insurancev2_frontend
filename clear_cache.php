<?php
echo "<h2>Clearing Laravel Cache...</h2>";

// Commands to run
$commands = ['route:clear', 'config:clear', 'cache:clear', 'view:clear'];

foreach($commands as $cmd) {
    echo "<p>Running: php artisan $cmd</p>";
    $output = [];
    exec("php artisan $cmd 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<span style='color: green;'>✓ Success</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Failed</span><br>";
    }
    
    if (!empty($output)) {
        echo "<small>" . implode('<br>', $output) . "</small><br>";
    }
    echo "<hr>";
}

echo "<h3>Cache clearing complete!</h3>";
echo "<p><strong>Important:</strong> Delete this file (clear_cache.php) after use for security.</p>";
echo "<p>Now try the document removal functionality again.</p>";
?>