<?php
// clear-cache.php - DELETE AFTER USE!
echo "🧹 Clearing Laravel Cache...<br>";

try {
    // Simple cache clearing without full Laravel bootstrap
    $cacheDir = __DIR__ . "/storage/framework/cache";
    $viewsDir = __DIR__ . "/storage/framework/views";
    $configDir = __DIR__ . "/bootstrap/cache";
    
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Cache cleared<br>";
    }
    
    if (is_dir($viewsDir)) {
        $files = glob($viewsDir . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Views cleared<br>";
    }
    
    if (is_dir($configDir)) {
        $files = glob($configDir . "/*");
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== ".gitignore") {
                unlink($file);
            }
        }
        echo "✅ Config cleared<br>";
    }
    
    echo "✅ All caches cleared successfully!<br>";
    echo "🚨 DELETE this file immediately for security!<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>
