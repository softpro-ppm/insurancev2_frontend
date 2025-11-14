<?php
/**
 * Simple Production Cache Clear
 * Upload to public/ folder and access via browser
 */

// Set proper working directory to Laravel root (one level up from public)
chdir(__DIR__ . '/..');

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Cache Clear</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;border-radius:8px;max-width:600px;margin:20px auto;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo "h1{color:#333;margin:0 0 20px 0;}";
echo ".success{color:#10b981;margin:5px 0;}";
echo ".error{color:#ef4444;margin:5px 0;}";
echo ".cmd{background:#1f2937;color:#10b981;padding:10px;border-radius:4px;margin:5px 0;font-family:monospace;}";
echo "</style></head><body>";

echo "<div class='box'><h1>🚀 Production Cache Clear</h1>";

// Function to run command
function runCmd($cmd) {
    echo "<div class='cmd'>$ {$cmd}</div>";
    exec($cmd . ' 2>&1', $output, $code);
    if ($code === 0) {
        echo "<div class='success'>✅ SUCCESS</div>";
    } else {
        echo "<div class='error'>❌ FAILED (Code: {$code})</div>";
    }
    if (!empty($output)) {
        echo "<pre style='background:#f9f9f9;padding:10px;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
    $output = [];
    flush();
    ob_flush();
}

// Clear all caches
ob_start();
runCmd('php artisan route:clear');
runCmd('php artisan config:clear');
runCmd('php artisan cache:clear');
runCmd('php artisan view:clear');
ob_end_flush();

echo "<div class='success' style='margin-top:20px;padding:15px;background:#d1fae5;border-radius:4px;'>";
echo "<strong>✅ All caches cleared!</strong><br><br>";
echo "<strong>Next steps:</strong><br>";
echo "1. Close ALL browser tabs<br>";
echo "2. Quit browser (Cmd+Q)<br>";
echo "3. Restart browser<br>";
echo "4. Login fresh and hard refresh (Cmd+Shift+R)<br>";
echo "5. <strong style='color:#ef4444;'>DELETE THIS FILE via File Manager!</strong>";
echo "</div>";

echo "</div></body></html>";
?>
