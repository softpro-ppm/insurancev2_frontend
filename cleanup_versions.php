<?php

/**
 * Web-based Policy Version Cleanup Script
 * 
 * This script can be run via web browser on Hostinger
 * Run this by visiting: https://yourdomain.com/cleanup_versions.php
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    use App\Models\Policy;
    use App\Models\PolicyVersion;
    
    echo "<h1>🧹 Policy Version Cleanup</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>";
    
    // Check if cleanup has already been run
    $policiesWithMultipleVersions = Policy::with('versions')
        ->get()
        ->filter(function ($policy) {
            return $policy->versions->count() > 1;
        });
    
    if ($policiesWithMultipleVersions->count() === 0) {
        echo "<p class='success'>✅ No duplicate versions found. All policies have only 1 version.</p>";
        exit;
    }
    
    echo "<h2>📊 Analysis Results</h2>";
    echo "<p class='info'>Policies with multiple versions: " . $policiesWithMultipleVersions->count() . "</p>";
    
    $totalVersionsToDelete = $policiesWithMultipleVersions->sum(function ($policy) {
        return $policy->versions->count() - 1;
    });
    
    echo "<p class='warning'>Total versions to delete: {$totalVersionsToDelete}</p>";
    
    echo "<h3>📋 Policies with duplicate versions:</h3>";
    echo "<ul>";
    foreach ($policiesWithMultipleVersions as $policy) {
        echo "<li>Policy #{$policy->id}: {$policy->customer_name} ({$policy->versions->count()} versions)</li>";
    }
    echo "</ul>";
    
    // Check if cleanup action is requested
    if (isset($_GET['action']) && $_GET['action'] === 'cleanup') {
        echo "<h2>🗑️ Starting cleanup...</h2>";
        
        $deletedCount = 0;
        $errors = [];
        
        foreach ($policiesWithMultipleVersions as $policy) {
            try {
                echo "<p>Processing Policy #{$policy->id}: {$policy->customer_name}...</p>";
                
                // Delete all versions except version 1
                $versionsToDelete = PolicyVersion::where('policy_id', $policy->id)
                    ->where('version_number', '>', 1)
                    ->get();
                    
                foreach ($versionsToDelete as $version) {
                    echo "<p style='margin-left: 20px;'>- Deleting version {$version->version_number} (created: {$version->version_created_at})</p>";
                    $version->delete();
                    $deletedCount++;
                }
                
                echo "<p class='success'>✅ Cleaned up Policy #{$policy->id}</p>";
                
            } catch (Exception $e) {
                $error = "Error processing Policy #{$policy->id}: " . $e->getMessage();
                $errors[] = $error;
                echo "<p class='error'>❌ {$error}</p>";
            }
        }
        
        echo "<h2>📊 Cleanup Results</h2>";
        echo "<p class='success'>Versions deleted: {$deletedCount}</p>";
        echo "<p class='error'>Errors encountered: " . count($errors) . "</p>";
        
        if (!empty($errors)) {
            echo "<h3>❌ Errors:</h3>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li class='error'>{$error}</li>";
            }
            echo "</ul>";
        }
        
        // Verify cleanup
        echo "<h2>🔍 Verifying cleanup...</h2>";
        $remainingDuplicates = Policy::with('versions')
            ->get()
            ->filter(function ($policy) {
                return $policy->versions->count() > 1;
            })
            ->count();
        
        if ($remainingDuplicates === 0) {
            echo "<p class='success'>✅ Cleanup successful! No policies have duplicate versions.</p>";
        } else {
            echo "<p class='warning'>⚠️ Warning: {$remainingDuplicates} policies still have multiple versions.</p>";
        }
        
        echo "<h2 class='success'>🎉 Cleanup completed!</h2>";
        echo "<p>You can now safely use the policy system without duplicate version histories.</p>";
        
    } else {
        echo "<h2>⚠️ Ready to Cleanup</h2>";
        echo "<p class='warning'>This will permanently delete duplicate policy versions!</p>";
        echo "<p>This script will:</p>";
        echo "<ul>";
        echo "<li>Keep only version 1 for each policy</li>";
        echo "<li>Delete all versions 2, 3, 4, etc.</li>";
        echo "<li>This action cannot be undone!</li>";
        echo "</ul>";
        
        echo "<p><a href='?action=cleanup' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗑️ DELETE DUPLICATE VERSIONS</a></p>";
    }
    
} catch (Exception $e) {
    echo "<h2 class='error'>❌ Error</h2>";
    echo "<p class='error'>Failed to run cleanup script: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
