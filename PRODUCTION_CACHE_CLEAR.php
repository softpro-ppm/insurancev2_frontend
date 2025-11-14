<?php
/**
 * Production Cache Clear Script
 * Upload this file to your Hostinger server and run it via browser
 * URL: https://v2insurance.softpromis.com/PRODUCTION_CACHE_CLEAR.php
 * 
 * IMPORTANT: DELETE THIS FILE AFTER USE FOR SECURITY!
 */

// Set proper working directory
chdir(__DIR__);

echo "<!DOCTYPE html>";
echo "<html><head><title>Cache Clear - Production</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f5f5f5;}";
echo ".container{background:white;padding:30px;border-radius:8px;max-width:800px;margin:0 auto;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo "h1{color:#333;margin-top:0;}";
echo ".success{color:#10b981;padding:10px;background:#d1fae5;border-radius:4px;margin:10px 0;}";
echo ".error{color:#ef4444;padding:10px;background:#fee2e2;border-radius:4px;margin:10px 0;}";
echo ".info{color:#3b82f6;padding:10px;background:#dbeafe;border-radius:4px;margin:10px 0;}";
echo ".command{background:#1f2937;color:#10b981;padding:15px;border-radius:4px;margin:10px 0;font-family:monospace;overflow-x:auto;}";
echo "</style></head><body>";
echo "<div class='container'>";
echo "<h1>🚀 Production Cache Clear</h1>";
echo "<div class='info'>Running cache clear commands...</div>";

// Function to run command and display output
function runCommand($command, $label) {
    echo "<div class='command'><strong>$ {$command}</strong></div>";
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<div class='success'>✅ {$label}: SUCCESS</div>";
    } else {
        echo "<div class='error'>❌ {$label}: FAILED (Code: {$returnCode})</div>";
    }
    
    if (!empty($output)) {
        echo "<pre style='background:#f9fafb;padding:10px;border-radius:4px;overflow-x:auto;'>";
        echo htmlspecialchars(implode("\n", $output));
        echo "</pre>";
    }
    
    // Clear output array for next command
    $output = [];
}

// Clear all Laravel caches
runCommand('php artisan route:clear', 'Route Cache');
runCommand('php artisan config:clear', 'Config Cache');
runCommand('php artisan cache:clear', 'Application Cache');
runCommand('php artisan view:clear', 'View Cache');
runCommand('php artisan optimize:clear', 'Optimize Cache');

// Check current Git status
echo "<h2>📊 Git Status</h2>";
runCommand('git log --oneline -5', 'Recent Commits');
runCommand('git status', 'Git Status');

// Display Laravel version and environment
echo "<h2>🔍 Environment Info</h2>";
runCommand('php artisan --version', 'Laravel Version');

echo "<div class='success'>";
echo "<h2>✅ Cache Clear Complete!</h2>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Close ALL browser tabs for v2insurance.softpromis.com</li>";
echo "<li>Quit your browser completely (not just close tabs)</li>";
echo "<li>Restart browser and open fresh window</li>";
echo "<li>Go to https://v2insurance.softpromis.com</li>";
echo "<li>Hard refresh (Cmd+Shift+R) after login</li>";
echo "<li><strong style='color:#ef4444;'>DELETE THIS FILE (PRODUCTION_CACHE_CLEAR.php) via File Manager for security!</strong></li>";
echo "</ol>";
echo "</div>";

echo "</div></body></html>";
?>

