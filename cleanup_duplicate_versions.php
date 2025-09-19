<?php

/**
 * Cleanup Script: Remove Duplicate Policy Versions
 * 
 * This script removes duplicate version histories that were created due to the bug
 * where every policy update (including document uploads) created a new version.
 * 
 * Strategy:
 * - Keep only the first version (version_number = 1) for each policy
 * - Remove all subsequent versions (version_number > 1) 
 * - This assumes the first version contains the original policy data
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;

echo "🧹 Policy Version Cleanup Script\n";
echo "================================\n\n";

// Safety check - ask for confirmation
echo "⚠️  WARNING: This will permanently delete duplicate policy versions!\n";
echo "This script will:\n";
echo "- Keep only version 1 for each policy\n";
echo "- Delete all versions 2, 3, 4, etc.\n";
echo "- This action cannot be undone!\n\n";

echo "Do you want to continue? (yes/no): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'yes') {
    echo "❌ Operation cancelled.\n";
    exit(0);
}

echo "\n🔍 Analyzing policy versions...\n";

// Get all policies with their version counts
$policiesWithVersions = Policy::with('versions')
    ->get()
    ->map(function ($policy) {
        $versionCount = $policy->versions->count();
        return [
            'id' => $policy->id,
            'customer_name' => $policy->customer_name,
            'version_count' => $versionCount,
            'versions' => $policy->versions->sortBy('version_number')
        ];
    })
    ->filter(function ($policy) {
        return $policy['version_count'] > 1; // Only policies with multiple versions
    });

$totalPolicies = $policiesWithVersions->count();
$totalVersionsToDelete = $policiesWithVersions->sum(function ($policy) {
    return $policy['version_count'] - 1; // Keep version 1, delete the rest
});

echo "📊 Analysis Results:\n";
echo "- Policies with multiple versions: {$totalPolicies}\n";
echo "- Total versions to delete: {$totalVersionsToDelete}\n";
echo "- Versions to keep: {$totalPolicies}\n\n";

if ($totalPolicies === 0) {
    echo "✅ No duplicate versions found. All policies have only 1 version.\n";
    exit(0);
}

echo "📋 Policies with duplicate versions:\n";
foreach ($policiesWithVersions as $policy) {
    echo "- Policy #{$policy['id']}: {$policy['customer_name']} ({$policy['version_count']} versions)\n";
}
echo "\n";

// Final confirmation
echo "⚠️  FINAL CONFIRMATION: Delete {$totalVersionsToDelete} duplicate versions?\n";
echo "Type 'DELETE' to confirm: ";
$handle = fopen("php://stdin", "r");
$finalConfirmation = trim(fgets($handle));
fclose($handle);

if ($finalConfirmation !== 'DELETE') {
    echo "❌ Operation cancelled.\n";
    exit(0);
}

echo "\n🗑️  Starting cleanup...\n";

$deletedCount = 0;
$errors = [];

foreach ($policiesWithVersions as $policy) {
    try {
        echo "Processing Policy #{$policy['id']}: {$policy['customer_name']}...\n";
        
        // Delete all versions except version 1
        $versionsToDelete = PolicyVersion::where('policy_id', $policy['id'])
            ->where('version_number', '>', 1)
            ->get();
            
        foreach ($versionsToDelete as $version) {
            echo "  - Deleting version {$version->version_number} (created: {$version->version_created_at})\n";
            $version->delete();
            $deletedCount++;
        }
        
        echo "  ✅ Cleaned up Policy #{$policy['id']}\n";
        
    } catch (Exception $e) {
        $error = "Error processing Policy #{$policy['id']}: " . $e->getMessage();
        $errors[] = $error;
        echo "  ❌ {$error}\n";
    }
}

echo "\n📊 Cleanup Results:\n";
echo "- Versions deleted: {$deletedCount}\n";
echo "- Errors encountered: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\n❌ Errors:\n";
    foreach ($errors as $error) {
        echo "- {$error}\n";
    }
}

// Verify cleanup
echo "\n🔍 Verifying cleanup...\n";
$remainingDuplicates = Policy::with('versions')
    ->get()
    ->filter(function ($policy) {
        return $policy->versions->count() > 1;
    })
    ->count();

if ($remainingDuplicates === 0) {
    echo "✅ Cleanup successful! No policies have duplicate versions.\n";
} else {
    echo "⚠️  Warning: {$remainingDuplicates} policies still have multiple versions.\n";
}

echo "\n🎉 Cleanup completed!\n";
echo "You can now safely use the policy system without duplicate version histories.\n";
