<?php
/**
 * Web-Accessible Production Export Script
 * Upload to: /domains/v2insurance.softpromis.com/public_html/
 * Visit: https://v2insurance.softpromis.com/run_production_export.php
 */

// Security: Simple password protection
$EXPORT_PASSWORD = 'export2025'; // Change this if needed

if (!isset($_GET['password']) || $_GET['password'] !== $EXPORT_PASSWORD) {
    die('
    <html>
    <head><title>Production Export</title></head>
    <body style="font-family: Arial; padding: 40px; background: #f5f5f5;">
        <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h1>🔐 Production Export Tool</h1>
            <p>Please enter the password to continue:</p>
            <form method="get">
                <input type="password" name="password" style="padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 4px;">
                <button type="submit" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Export</button>
            </form>
            <p style="color: #999; font-size: 12px; margin-top: 20px;">Default password: export2025</p>
        </div>
    </body>
    </html>
    ');
}

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Production Export - Running...</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; color: #0c5460; }
        .download-btn { display: inline-block; padding: 15px 30px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
        .download-btn:hover { background: #45a049; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; padding: 15px; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔄 Production Export Tool</h1>
    
    <?php
    try {
        echo '<div class="status info">📊 Starting export process...</div>';
        flush();
        
        // Count policies
        $totalPolicies = \App\Models\Policy::count();
        $totalRenewals = \App\Models\Renewal::count();
        
        echo '<div class="status info">✅ Found ' . $totalPolicies . ' policies and ' . $totalRenewals . ' renewals</div>';
        flush();
        
        if ($totalPolicies == 0) {
            echo '<div class="status error">❌ No policies found to export!</div>';
            exit;
        }
        
        echo '<div class="status info">📥 Exporting data...</div>';
        flush();
        
        // Export policies
        $policies = \App\Models\Policy::all()->toArray();
        $renewals = \App\Models\Renewal::all()->toArray();
        
        $export = [
            'exported_at' => now()->toDateTimeString(),
            'total_policies' => count($policies),
            'total_renewals' => count($renewals),
            'server' => 'production',
            'policies' => $policies,
            'renewals' => $renewals
        ];
        
        $outputFile = base_path('policies_export.json');
        file_put_contents($outputFile, json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $fileSize = filesize($outputFile);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        
        echo '<div class="status success">';
        echo '<h2>✅ Export Completed Successfully!</h2>';
        echo '<p><strong>Exported:</strong> ' . $totalPolicies . ' policies, ' . $totalRenewals . ' renewals</p>';
        echo '<p><strong>File Size:</strong> ' . $fileSizeMB . ' MB</p>';
        echo '<p><strong>Exported At:</strong> ' . now()->toDateTimeString() . '</p>';
        echo '</div>';
        
        echo '<a href="/policies_export.json" class="download-btn" download>📥 Download policies_export.json</a>';
        
        echo '<div class="warning">';
        echo '<strong>⚠️ IMPORTANT SECURITY STEPS:</strong><br>';
        echo '1. Download the <code>policies_export.json</code> file<br>';
        echo '2. DELETE <code>run_production_export.php</code> from server<br>';
        echo '3. DELETE <code>policies_export.json</code> from server<br>';
        echo '4. The export file contains sensitive customer data!';
        echo '</div>';
        
        echo '<h3>📋 Next Steps:</h3>';
        echo '<ol>';
        echo '<li>Click the download button above</li>';
        echo '<li>Save the file to your local project root</li>';
        echo '<li>Run: <code>php import_policies_local.php</code></li>';
        echo '<li>Delete this script and the JSON file from production</li>';
        echo '</ol>';
        
    } catch (\Exception $e) {
        echo '<div class="status error">';
        echo '<h2>❌ Error During Export</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
    ?>
    
</div>
</body>
</html>

