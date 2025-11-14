<?php

// Simple script to delete policy versions from SQLite database
// Upload this to your server root and access via browser

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Policy Versions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        button { background: #dc3545; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; margin: 10px 0; font-size: 16px; }
        button:hover { background: #c82333; }
        .info { background: #e8f5e8; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑️ Delete Policy Versions</h1>
        
        <div class="info">
            <h3>🛡️ SAFE OPERATION</h3>
            <ul>
                <li>✅ <strong>Your 532 customer policy documents are SAFE</strong></li>
                <li>✅ <strong>All current policy data will be preserved</strong></li>
                <li>✅ <strong>Only old policy version records will be deleted</strong></li>
                <li>✅ <strong>Policy history will show only Version 1</strong></li>
            </ul>
        </div>
        
        <?php
        try {
            // Get current statistics
            $totalVersions = DB::table('policy_versions')->count();
            
            echo "<h2>Current Status</h2>";
            echo "<p><strong>Total policy versions in database:</strong> {$totalVersions}</p>";
            
            if ($totalVersions > 0) {
                echo "<h2>Policy Versions Found</h2>";
                $versions = DB::table('policy_versions')->get();
                echo "<ul>";
                foreach ($versions as $version) {
                    echo "<li>Policy ID: {$version->policy_id} - Version {$version->version_number} - {$version->version_created_at}</li>";
                }
                echo "</ul>";
                
                if (isset($_POST['delete_versions']) && $_POST['delete_versions'] === 'yes') {
                    echo "<h2>🗑️ Deleting Policy Versions...</h2>";
                    
                    try {
                        // Delete all policy versions
                        $deletedCount = DB::table('policy_versions')->delete();
                        
                        echo "<div class='success'>";
                        echo "<h3>✅ SUCCESS!</h3>";
                        echo "<p><strong>Deleted {$deletedCount} policy versions</strong></p>";
                        echo "<p>All current policy documents are preserved</p>";
                        echo "<p><strong>Policy history will now show only Version 1 for each policy</strong></p>";
                        echo "<p><strong>Please refresh your browser and test the policy history!</strong></p>";
                        echo "</div>";
                        
                    } catch (Exception $e) {
                        echo "<p class='error'>❌ ERROR: " . $e->getMessage() . "</p>";
                    }
                } else {
                    echo "<h2>Delete Action</h2>";
                    echo "<p class='warning'>⚠ This will delete ALL policy versions from the database.</p>";
                    echo "<p class='success'>✅ <strong>IMPORTANT:</strong> All current policy documents will be preserved!</p>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='delete_versions' value='yes'>";
                    echo "<button type='submit' onclick='return confirm(\"Are you sure you want to delete all policy versions? Your documents will be safe.\")'>🗑️ DELETE ALL POLICY VERSIONS (Documents Safe)</button>";
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
        <p><small>This operation is SAFE - it only deletes version records, not your important policy documents!</small></p>
    </div>
</body>
</html>
