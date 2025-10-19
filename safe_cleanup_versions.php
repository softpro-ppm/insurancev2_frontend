<?php

// SAFE Policy Version Cleanup Script
// This script will ONLY delete policy versions but PRESERVE all current policy documents
// Run this to clean up versions while keeping your 532 customer policy documents safe

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
    <title>SAFE Policy Version Cleanup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 10px 0; }
        button:hover { background: #0056b3; }
        .danger { background: #dc3545; }
        .danger:hover { background: #c82333; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .document-status { background: #e8f5e8; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ SAFE Policy Version Cleanup</h1>
        <p class="info"><strong>This script will:</strong></p>
        <ul>
            <li>✅ <strong>PRESERVE</strong> all current policy documents (your 532 customer policies are safe)</li>
            <li>✅ <strong>DELETE</strong> only old policy version records from database</li>
            <li>✅ <strong>KEEP</strong> all current policy data intact</li>
            <li>✅ <strong>SHOW</strong> only Version 1 in policy history</li>
        </ul>
        
        <?php
        try {
            // Get current statistics
            $totalPolicies = Policy::count();
            $totalVersions = PolicyVersion::count();
            
            echo "<h2>Current Status</h2>";
            echo "<p><strong>Total policies:</strong> {$totalPolicies}</p>";
            echo "<p><strong>Total policy versions:</strong> {$totalVersions}</p>";
            
            // Check document status
            $policiesWithDocs = Policy::where(function($query) {
                $query->whereNotNull('policy_copy_path')
                      ->orWhereNotNull('rc_copy_path')
                      ->orWhereNotNull('aadhar_copy_path')
                      ->orWhereNotNull('pan_copy_path');
            })->count();
            
            echo "<div class='document-status'>";
            echo "<h3>📄 Document Status</h3>";
            echo "<p><strong>Policies with documents:</strong> {$policiesWithDocs}</p>";
            echo "<p class='success'>✅ All current policy documents are SAFE and will be preserved</p>";
            echo "</div>";
            
            if ($totalVersions > 0) {
                echo "<h2>Policy Versions Found</h2>";
                $allVersions = PolicyVersion::with('policy')->get();
                echo "<ul>";
                foreach ($allVersions as $version) {
                    echo "<li>Policy ID: {$version->policy_id} ({$version->policy->customer_name}) - Version {$version->version_number} - {$version->version_created_at}</li>";
                }
                echo "</ul>";
                
                if (isset($_POST['cleanup']) && $_POST['cleanup'] === 'yes') {
                    echo "<h2>🧹 Cleaning Up...</h2>";
                    
                    DB::beginTransaction();
                    try {
                        $deletedCount = 0;
                        foreach ($allVersions as $version) {
                            echo "<p>Deleting Version {$version->version_number} for Policy {$version->policy_id}...</p>";
                            $version->delete();
                            $deletedCount++;
                        }
                        DB::commit();
                        
                        echo "<div class='success'>";
                        echo "<h3>✅ SUCCESS!</h3>";
                        echo "<p>Deleted {$deletedCount} policy versions</p>";
                        echo "<p>All current policy documents are preserved</p>";
                        echo "<p><strong>Policy history will now show only Version 1 for each policy</strong></p>";
                        echo "<p><strong>Please refresh your browser and test the policy history!</strong></p>";
                        echo "</div>";
                        
                    } catch (Exception $e) {
                        DB::rollback();
                        echo "<p class='error'>❌ ERROR: " . $e->getMessage() . "</p>";
                    }
                } else {
                    echo "<h2>Cleanup Action</h2>";
                    echo "<p class='warning'>⚠ This will delete ALL policy versions from the database.</p>";
                    echo "<p class='success'>✅ <strong>IMPORTANT:</strong> All current policy documents will be preserved!</p>";
                    echo "<p>The policy history will then show only the current policy data as Version 1.</p>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='cleanup' value='yes'>";
                    echo "<button type='submit' class='danger' onclick='return confirm(\"Are you sure you want to delete all policy versions? Your documents will be safe.\")'>Delete All Policy Versions (Documents Safe)</button>";
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
        <p><small>This cleanup is SAFE - it only deletes version records, not your important policy documents!</small></p>
    </div>
</body>
</html>
