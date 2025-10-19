<?php

// Simple web-based policy version cleanup
// Access this via: yourwebsite.com/cleanup_versions_web.php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use App\Models\PolicyVersion;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Policy Version Cleanup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 10px 0; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Policy Version Cleanup</h1>
        
        <?php
        try {
            // Get current statistics
            $totalPolicies = Policy::count();
            $totalVersions = PolicyVersion::count();
            
            echo "<h2>Current Status</h2>";
            echo "<p><strong>Total policies:</strong> {$totalPolicies}</p>";
            echo "<p><strong>Total policy versions:</strong> {$totalVersions}</p>";
            
            if ($totalVersions > 0) {
                echo "<h2>Policy Versions Found</h2>";
                $allVersions = PolicyVersion::with('policy')->get();
                echo "<ul>";
                foreach ($allVersions as $version) {
                    echo "<li>Policy ID: {$version->policy_id} ({$version->policy->customer_name}) - Version {$version->version_number} - {$version->version_created_at}</li>";
                }
                echo "</ul>";
                
                if (isset($_POST['cleanup']) && $_POST['cleanup'] === 'yes') {
                    echo "<h2>Cleaning Up...</h2>";
                    
                    DB::beginTransaction();
                    try {
                        $deletedCount = 0;
                        foreach ($allVersions as $version) {
                            $version->delete();
                            $deletedCount++;
                        }
                        DB::commit();
                        
                        echo "<p class='success'>✅ SUCCESS: Deleted {$deletedCount} policy versions</p>";
                        echo "<p class='success'>✅ Policy history will now show only Version 1 for each policy</p>";
                        echo "<p><strong>Please refresh your browser and test the policy history!</strong></p>";
                        
                    } catch (Exception $e) {
                        DB::rollback();
                        echo "<p class='error'>❌ ERROR: " . $e->getMessage() . "</p>";
                    }
                } else {
                    echo "<h2>Cleanup Action</h2>";
                    echo "<p class='warning'>⚠ This will delete ALL policy versions from the database.</p>";
                    echo "<p>The policy history will then show only the current policy data as Version 1.</p>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='cleanup' value='yes'>";
                    echo "<button type='submit' onclick='return confirm(\"Are you sure you want to delete all policy versions?\")'>Delete All Policy Versions</button>";
                    echo "</form>";
                }
            } else {
                echo "<p class='success'>✅ No policy versions found. Database is already clean!</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ ERROR: " . $e->getMessage() . "</p>";
        }
        ?>
        
        <hr>
        <p><small>After cleanup, refresh your browser and test the policy history modal.</small></p>
    </div>
</body>
</html>
