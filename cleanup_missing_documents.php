<?php

/**
 * Cleanup Missing Documents Script
 * 
 * This script removes database references to missing document files.
 * Run this AFTER restoring files or when you want to clean up missing file references.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;

echo "=== Insurance Management System - Missing Documents Cleanup ===\n\n";

// Clean up policies with missing documents
echo "1. Cleaning up policies with missing documents...\n";
$cleanedPolicies = 0;

$policies = Policy::where(function($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($policies as $policy) {
    $updated = false;
    $documentFields = ['policy_copy_path', 'rc_copy_path', 'aadhar_copy_path', 'pan_copy_path'];
    
    foreach ($documentFields as $field) {
        if ($policy->$field) {
            $fullPath = storage_path('app/' . $policy->$field);
            if (!file_exists($fullPath)) {
                echo "  Removing missing document reference: Policy {$policy->id}, {$field} = {$policy->$field}\n";
                $policy->$field = null;
                $updated = true;
            }
        }
    }
    
    if ($updated) {
        $policy->save();
        $cleanedPolicies++;
    }
}

echo "✅ Cleaned up {$cleanedPolicies} policies with missing document references.\n\n";

// Clean up policy versions with missing documents
echo "2. Cleaning up policy versions with missing documents...\n";
$cleanedVersions = 0;

$versions = PolicyVersion::where(function($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($versions as $version) {
    $updated = false;
    $documentFields = ['policy_copy_path', 'rc_copy_path', 'aadhar_copy_path', 'pan_copy_path'];
    
    foreach ($documentFields as $field) {
        if ($version->$field) {
            $fullPath = storage_path('app/' . $version->$field);
            if (!file_exists($fullPath)) {
                echo "  Removing missing document reference: Version {$version->id}, {$field} = {$version->$field}\n";
                $version->$field = null;
                $updated = true;
            }
        }
    }
    
    if ($updated) {
        $version->save();
        $cleanedVersions++;
    }
}

echo "✅ Cleaned up {$cleanedVersions} policy versions with missing document references.\n\n";

echo "=== Cleanup Complete ===\n";
echo "Total cleaned policies: {$cleanedPolicies}\n";
echo "Total cleaned versions: {$cleanedVersions}\n\n";

echo "Note: This script only removes database references to missing files.\n";
echo "If you restore the missing files later, you'll need to update the database manually.\n";
