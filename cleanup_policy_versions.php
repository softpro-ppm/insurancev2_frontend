<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Policy;
use App\Models\PolicyVersion;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== POLICY VERSION CLEANUP SCRIPT ===\n\n";

try {
    // Get all policies that have multiple versions
    $policiesWithMultipleVersions = Policy::has('versions', '>', 1)->with('versions')->get();
    
    echo "Found " . $policiesWithMultipleVersions->count() . " policies with multiple versions.\n\n";
    
    if ($policiesWithMultipleVersions->isEmpty()) {
        echo "No policies with multiple versions found. Nothing to clean up.\n";
        exit(0);
    }
    
    $totalVersionsToDelete = 0;
    $totalDocumentsToClean = 0;
    
    // Process each policy
    foreach ($policiesWithMultipleVersions as $policy) {
        echo "Processing Policy ID: {$policy->id} ({$policy->customer_name})\n";
        echo "  Current versions: " . $policy->versions->count() . "\n";
        
        // Get all versions ordered by version number (descending)
        $versions = $policy->versions()->orderBy('version_number', 'desc')->get();
        
        // Keep the latest version (first in the ordered list)
        $latestVersion = $versions->first();
        $versionsToDelete = $versions->skip(1); // Skip the first (latest) version
        
        echo "  Keeping: Version {$latestVersion->version_number} (created: {$latestVersion->version_created_at})\n";
        echo "  Deleting: " . $versionsToDelete->count() . " older versions\n";
        
        // Process each version to be deleted
        foreach ($versionsToDelete as $version) {
            echo "    Deleting Version {$version->version_number}...\n";
            
            // Count documents that will be cleaned up
            $documents = [
                $version->policy_copy_path,
                $version->rc_copy_path,
                $version->aadhar_copy_path,
                $version->pan_copy_path
            ];
            
            $documentCount = 0;
            foreach ($documents as $docPath) {
                if (!empty($docPath) && Storage::exists($docPath)) {
                    $documentCount++;
                }
            }
            
            if ($documentCount > 0) {
                echo "      Will clean up {$documentCount} documents from this version\n";
                $totalDocumentsToClean += $documentCount;
            }
            
            // Delete the version (this will also handle document cleanup if cascade is set up)
            $version->delete();
            $totalVersionsToDelete++;
        }
        
        echo "  ✓ Policy {$policy->id} cleanup completed\n\n";
    }
    
    echo "=== CLEANUP SUMMARY ===\n";
    echo "Total versions deleted: {$totalVersionsToDelete}\n";
    echo "Total documents cleaned up: {$totalDocumentsToClean}\n";
    echo "Policies processed: " . $policiesWithMultipleVersions->count() . "\n\n";
    
    // Verify the cleanup
    echo "=== VERIFICATION ===\n";
    $remainingVersions = PolicyVersion::count();
    $policiesWithMultipleVersions = Policy::has('versions', '>', 1)->count();
    
    echo "Remaining policy versions: {$remainingVersions}\n";
    echo "Policies with multiple versions: {$policiesWithMultipleVersions}\n";
    
    if ($policiesWithMultipleVersions === 0) {
        echo "✓ SUCCESS: All policies now have only one version (the latest one)\n";
    } else {
        echo "⚠ WARNING: Some policies still have multiple versions\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== CLEANUP COMPLETED ===\n";