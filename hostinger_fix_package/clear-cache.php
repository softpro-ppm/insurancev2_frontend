<?php
// clear-cache.php - DELETE THIS FILE AFTER USE!
echo "🧹 Clearing Laravel Cache...\n";

try {
    require_once '../bootstrap/app.php';
    $app = require_once '../bootstrap/app.php';
    
    // Clear all caches
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    
    echo "✅ Cache cleared successfully!\n";
    echo "🚨 IMPORTANT: Delete this file immediately for security!\n";
    
} catch (Exception $e) {
    echo "❌ Error clearing cache: " . $e->getMessage() . "\n";
    echo "Please check your Laravel installation.\n";
}
?>
