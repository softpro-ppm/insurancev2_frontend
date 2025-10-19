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

echo "=== ADVANCED POLICY VERSION CLEANUP SCRIPT ===\n\n";

try {
    // Get statistics before cleanup
    $totalPolicies = Policy::count();
    $totalVersions = PolicyVersion::count();
    $policiesWithMultipleVersions = Policy::has('versions', '>', 1)->with('versions')->get();
    
    echo "BEFORE CLEANUP:\n";
    echo "  Total policies: {$totalPolicies}\n";
    echo "  Total policy versions: {$totalVersions}\n";
    echo "  Policies with multiple versions: " . $policiesWithMultipleVersions->count() . "\n\n";
    
    if ($policiesWithMultipleVersions->isEmpty()) {
        echo "No policies with multiple versions found. Nothing to clean up.\n";
        exit(0);
    }
    
    $totalVersionsToDelete = 0;
    $totalDocumentsToClean = 0;
    $deletedDocumentPaths = [];
    
    // Start database transaction
    DB::beginTransaction();
    
    try {
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
                
                // Collect documents that will be cleaned up
                $documents = [
                    'policy_copy' => $version->policy_copy_path,
                    'rc_copy' => $version->rc_copy_path,
                    'aadhar_copy' => $version->aadhar_copy_path,
                    'pan_copy' => $version->pan_copy_path
                ];
                
                $documentCount = 0;
                foreach ($documents as $docType => $docPath) {
                    if (!empty($docPath) && Storage::exists($docPath)) {
                        echo "      Found {$docType}: {$docPath}\n";
                        
                        // Check if this document is used by any other version or the current policy
                        $isUsedElsewhere = false;
                        
                        // Check if used by current policy
                        if ($policy->policy_copy_path === $docPath || 
                            $policy->rc_copy_path === $docPath || 
                            $policy->aadhar_copy_path === $docPath || 
                            $policy->pan_copy_path === $docPath) {
                            $isUsedElsewhere = true;
                            echo "        → Document is used by current policy, keeping file\n";
                        }
                        
                        // Check if used by other versions of the same policy
                        if (!$isUsedElsewhere) {
                            $otherVersions = PolicyVersion::where('policy_id', $policy->id)
                                ->where('id', '!=', $version->id)
                                ->get();
                            
                            foreach ($otherVersions as $otherVersion) {
                                if ($otherVersion->policy_copy_path === $docPath || 
                                    $otherVersion->rc_copy_path === $docPath || 
                                    $otherVersion->aadhar_copy_path === $docPath || 
                                    $otherVersion->pan_copy_path === $docPath) {
                                    $isUsedElsewhere = true;
                                    echo "        → Document is used by another version, keeping file\n";
                                    break;
                                }
                            }
                        }
                        
                        // Check if used by any other policy
                        if (!$isUsedElsewhere) {
                            $otherPolicies = Policy::where('id', '!=', $policy->id)->get();
                            foreach ($otherPolicies as $otherPolicy) {
                                if ($otherPolicy->policy_copy_path === $docPath || 
                                    $otherPolicy->rc_copy_path === $docPath || 
                                    $otherPolicy->aadhar_copy_path === $docPath || 
                                    $otherPolicy->pan_copy_path === $docPath) {
                                    $isUsedElsewhere = true;
                                    echo "        → Document is used by another policy, keeping file\n";
                                    break;
                                }
                            }
                        }
                        
                        // Check if used by any other policy version
                        if (!$isUsedElsewhere) {
                            $otherPolicyVersions = PolicyVersion::where('policy_id', '!=', $policy->id)->get();
                            foreach ($otherPolicyVersions as $otherPolicyVersion) {
                                if ($otherPolicyVersion->policy_copy_path === $docPath || 
                                    $otherPolicyVersion->rc_copy_path === $docPath || 
                                    $otherPolicyVersion->aadhar_copy_path === $docPath || 
                                    $otherPolicyVersion->pan_copy_path === $docPath) {
                                    $isUsedElsewhere = true;
                                    echo "        → Document is used by another policy version, keeping file\n";
                                    break;
                                }
                            }
                        }
                        
                        if (!$isUsedElsewhere) {
                            echo "        → Document is safe to delete\n";
                            $deletedDocumentPaths[] = $docPath;
                            $documentCount++;
                        }
                    }
                }
                
                if ($documentCount > 0) {
                    echo "      Will clean up {$documentCount} unused documents from this version\n";
                    $totalDocumentsToClean += $documentCount;
                }
                
                // Delete the version
                $version->delete();
                $totalVersionsToDelete++;
            }
            
            echo "  ✓ Policy {$policy->id} cleanup completed\n\n";
        }
        
        // Clean up unused document files
        echo "Cleaning up unused document files...\n";
        $actuallyDeletedFiles = 0;
        foreach ($deletedDocumentPaths as $docPath) {
            if (Storage::exists($docPath)) {
                Storage::delete($docPath);
                $actuallyDeletedFiles++;
                echo "  Deleted: {$docPath}\n";
            }
        }
        
        // Commit the transaction
        DB::commit();
        
        echo "\n=== CLEANUP SUMMARY ===\n";
        echo "Total versions deleted: {$totalVersionsToDelete}\n";
        echo "Total document files cleaned up: {$actuallyDeletedFiles}\n";
        echo "Policies processed: " . $policiesWithMultipleVersions->count() . "\n\n";
        
    } catch (Exception $e) {
        // Rollback the transaction
        DB::rollback();
        throw $e;
    }
    
    // Verify the cleanup
    echo "=== VERIFICATION ===\n";
    $remainingVersions = PolicyVersion::count();
    $policiesWithMultipleVersions = Policy::has('versions', '>', 1)->count();
    
    echo "AFTER CLEANUP:\n";
    echo "  Total policies: {$totalPolicies}\n";
    echo "  Remaining policy versions: {$remainingVersions}\n";
    echo "  Policies with multiple versions: {$policiesWithMultipleVersions}\n";
    
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
