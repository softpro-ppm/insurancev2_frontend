<?php

// Policy Version Cleanup Script for Live Server
// This script will delete ALL policy versions from the database
// Run this directly on your live server

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use App\Models\PolicyVersion;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== POLICY VERSION CLEANUP FOR LIVE SERVER ===\n\n";

try {
    // Get statistics before cleanup
    $totalPolicies = Policy::count();
    $totalVersions = PolicyVersion::count();
    
    echo "BEFORE CLEANUP:\n";
    echo "  Total policies: {$totalPolicies}\n";
    echo "  Total policy versions: {$totalVersions}\n\n";
    
    if ($totalVersions === 0) {
        echo "No policy versions found. Nothing to clean up.\n";
        exit(0);
    }
    
    // Show what will be deleted
    echo "VERSIONS TO BE DELETED:\n";
    $allVersions = PolicyVersion::with('policy')->get();
    foreach ($allVersions as $version) {
        echo "  Policy ID: {$version->policy_id} ({$version->policy->customer_name}) - Version {$version->version_number} - {$version->version_created_at}\n";
    }
    echo "\n";
    
    // Confirmation
    echo "This will delete ALL policy versions from the database.\n";
    echo "The policy history will then show only the current policy data as Version 1.\n\n";
    
    echo "Deleting all policy versions...\n\n";
    
    // Start database transaction
    DB::beginTransaction();
    
    try {
        $deletedVersions = 0;
        
        // Delete all policy versions
        foreach ($allVersions as $version) {
            echo "Deleting Version {$version->version_number} for Policy {$version->policy_id}...\n";
            $version->delete();
            $deletedVersions++;
        }
        
        // Commit the transaction
        DB::commit();
        
        echo "\n=== CLEANUP SUMMARY ===\n";
        echo "Total versions deleted: {$deletedVersions}\n";
        echo "Policies processed: {$totalPolicies}\n\n";
        
    } catch (Exception $e) {
        // Rollback the transaction
        DB::rollback();
        throw $e;
    }
    
    // Verify the cleanup
    echo "=== VERIFICATION ===\n";
    $remainingVersions = PolicyVersion::count();
    
    echo "AFTER CLEANUP:\n";
    echo "  Total policies: {$totalPolicies}\n";
    echo "  Remaining policy versions: {$remainingVersions}\n";
    
    if ($remainingVersions === 0) {
        echo "✅ SUCCESS: All policy versions have been deleted\n";
        echo "✅ Policy history will now show only Version 1 for each policy\n";
    } else {
        echo "⚠ WARNING: Some policy versions still exist\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== CLEANUP COMPLETED ===\n";
echo "Now refresh your browser and test the policy history!\n";
