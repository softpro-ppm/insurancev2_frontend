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

echo "=== COMPLETE POLICY VERSION CLEANUP SCRIPT ===\n\n";

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
    echo "This will delete ALL policy versions and keep only the current policy data.\n";
    echo "The policy history modal will show only the current version.\n\n";
    
    if (!isset($argv[1]) || $argv[1] !== '--force') {
        echo "To proceed, run: php cleanup_all_policy_versions.php --force\n";
        echo "Or type 'yes' to continue: ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) !== 'yes') {
            echo "Cleanup cancelled.\n";
            exit(0);
        }
        fclose($handle);
    }
    
    echo "\nStarting cleanup...\n\n";
    
    // Start database transaction
    DB::beginTransaction();
    
    try {
        $deletedVersions = 0;
        $deletedDocuments = 0;
        
        // Process each version
        foreach ($allVersions as $version) {
            echo "Deleting Version {$version->version_number} for Policy {$version->policy_id}...\n";
            
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
                    // Check if this document is used by the current policy
                    $policy = Policy::find($version->policy_id);
                    $isUsedByCurrentPolicy = false;
                    
                    if ($policy && (
                        $policy->policy_copy_path === $docPath || 
                        $policy->rc_copy_path === $docPath || 
                        $policy->aadhar_copy_path === $docPath || 
                        $policy->pan_copy_path === $docPath
                    )) {
                        $isUsedByCurrentPolicy = true;
                    }
                    
                    // Check if used by any other policy
                    $isUsedElsewhere = false;
                    if (!$isUsedByCurrentPolicy) {
                        $otherPolicies = Policy::where('id', '!=', $version->policy_id)->get();
                        foreach ($otherPolicies as $otherPolicy) {
                            if ($otherPolicy->policy_copy_path === $docPath || 
                                $otherPolicy->rc_copy_path === $docPath || 
                                $otherPolicy->aadhar_copy_path === $docPath || 
                                $otherPolicy->pan_copy_path === $docPath) {
                                $isUsedElsewhere = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$isUsedByCurrentPolicy && !$isUsedElsewhere) {
                        Storage::delete($docPath);
                        $deletedDocuments++;
                        echo "  Deleted document: {$docPath}\n";
                    } else {
                        echo "  Kept document (used elsewhere): {$docPath}\n";
                    }
                    $documentCount++;
                }
            }
            
            if ($documentCount > 0) {
                echo "  Processed {$documentCount} documents from this version\n";
            }
            
            // Delete the version
            $version->delete();
            $deletedVersions++;
        }
        
        // Commit the transaction
        DB::commit();
        
        echo "\n=== CLEANUP SUMMARY ===\n";
        echo "Total versions deleted: {$deletedVersions}\n";
        echo "Total document files deleted: {$deletedDocuments}\n";
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
        echo "✓ SUCCESS: All policy versions have been deleted\n";
        echo "✓ Policy history will now show only the current version for each policy\n";
    } else {
        echo "⚠ WARNING: Some policy versions still exist\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== CLEANUP COMPLETED ===\n";
