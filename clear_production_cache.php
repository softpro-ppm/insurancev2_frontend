<?php
/**
 * Clear Production Cache
 * Upload to Hostinger and visit: https://v2insurance.softpromis.com/clear_production_cache.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Production Cache</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; color: #0c5460; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; padding: 15px; margin: 20px 0; border-radius: 5px; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧹 Clear Production Cache</h1>
    
    <?php
    try {
        echo '<div class="status info">📊 Starting cache clearing process...</div>';
        flush();
        
        // Clear config cache
        Artisan::call('config:clear');
        echo '<div class="status success">✅ Config cache cleared</div>';
        flush();
        
        // Clear application cache
        Artisan::call('cache:clear');
        echo '<div class="status success">✅ Application cache cleared</div>';
        flush();
        
        // Clear view cache
        Artisan::call('view:clear');
        echo '<div class="status success">✅ View cache cleared</div>';
        flush();
        
        // Clear route cache
        Artisan::call('route:clear');
        echo '<div class="status success">✅ Route cache cleared</div>';
        flush();
        
        // Optimize
        Artisan::call('optimize:clear');
        echo '<div class="status success">✅ Optimization cleared</div>';
        flush();
        
        echo '<div class="status success"><h2>✅ All Caches Cleared Successfully!</h2></div>';
        
        echo '<div class="warning">';
        echo '<strong>⚠️ IMPORTANT:</strong><br>';
        echo '1. DELETE this file immediately for security<br>';
        echo '2. Visit: <a href="/followups" target="_blank">https://v2insurance.softpromis.com/followups</a><br>';
        echo '3. Check browser console (F12) for any errors<br>';
        echo '4. Check Network tab - verify API calls are working';
        echo '</div>';
        
        // Debug info
        echo '<h3>📋 Debug Information:</h3>';
        echo '<pre>';
        echo "Current Time: " . now()->toDateTimeString() . "\n";
        echo "Timezone: " . config('app.timezone') . "\n";
        echo "App Environment: " . config('app.env') . "\n";
        echo "Database: " . config('database.default') . "\n";
        echo "\n";
        echo "Total Policies: " . \App\Models\Policy::count() . "\n";
        echo "Policies with end_date: " . \App\Models\Policy::whereNotNull('end_date')->count() . "\n";
        echo "\n";
        echo "Last Month Range: " . now()->subMonth()->startOfMonth()->format('Y-m-d') . " to " . now()->subMonth()->endOfMonth()->format('Y-m-d') . "\n";
        echo "Current Month Range: " . now()->startOfMonth()->format('Y-m-d') . " to " . now()->endOfMonth()->format('Y-m-d') . "\n";
        echo "Next Month Range: " . now()->addMonth()->startOfMonth()->format('Y-m-d') . " to " . now()->addMonth()->endOfMonth()->format('Y-m-d') . "\n";
        echo '</pre>';
        
    } catch (\Exception $e) {
        echo '<div class="status error">';
        echo '<h2>❌ Error</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
    ?>
    
</div>
</body>
</html>
