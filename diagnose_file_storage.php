<?php

/**
 * File Storage Diagnostic Script
 * 
 * This script helps diagnose file storage issues in the insurance management system.
 * Run this on the production server to check for missing files and provide solutions.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;

echo "=== Insurance Management System - File Storage Diagnostic ===\n\n";

// Check policies with missing documents
echo "1. Checking policies with missing documents...\n";
$policiesWithMissingDocs = [];

$policies = Policy::where(function($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($policies as $policy) {
    $missingDocs = [];
    $documentFields = [
        'policy_copy_path' => 'Policy Document',
        'rc_copy_path' => 'RC Document', 
        'aadhar_copy_path' => 'Aadhar Document',
        'pan_copy_path' => 'PAN Document'
    ];
    
    foreach ($documentFields as $field => $label) {
        if ($policy->$field) {
            $fullPath = storage_path('app/' . $policy->$field);
            if (!file_exists($fullPath)) {
                $missingDocs[] = [
                    'type' => $label,
                    'path' => $policy->$field,
                    'full_path' => $fullPath
                ];
            }
        }
    }
    
    if (!empty($missingDocs)) {
        $policiesWithMissingDocs[] = [
            'policy_id' => $policy->id,
            'customer_name' => $policy->customer_name,
            'vehicle_number' => $policy->vehicle_number,
            'missing_docs' => $missingDocs
        ];
    }
}

if (empty($policiesWithMissingDocs)) {
    echo "✅ All policy documents are found on disk.\n\n";
} else {
    echo "❌ Found " . count($policiesWithMissingDocs) . " policies with missing documents:\n\n";
    
    foreach ($policiesWithMissingDocs as $policyData) {
        echo "Policy ID: {$policyData['policy_id']}\n";
        echo "Customer: {$policyData['customer_name']}\n";
        echo "Vehicle: {$policyData['vehicle_number']}\n";
        echo "Missing Documents:\n";
        
        foreach ($policyData['missing_docs'] as $doc) {
            echo "  - {$doc['type']}: {$doc['path']}\n";
            echo "    Expected at: {$doc['full_path']}\n";
        }
        echo "\n";
    }
}

// Check policy versions with missing documents
echo "2. Checking policy versions with missing documents...\n";
$versionsWithMissingDocs = [];

$versions = PolicyVersion::where(function($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($versions as $version) {
    $missingDocs = [];
    $documentFields = [
        'policy_copy_path' => 'Policy Document',
        'rc_copy_path' => 'RC Document',
        'aadhar_copy_path' => 'Aadhar Document', 
        'pan_copy_path' => 'PAN Document'
    ];
    
    foreach ($documentFields as $field => $label) {
        if ($version->$field) {
            $fullPath = storage_path('app/' . $version->$field);
            if (!file_exists($fullPath)) {
                $missingDocs[] = [
                    'type' => $label,
                    'path' => $version->$field,
                    'full_path' => $fullPath
                ];
            }
        }
    }
    
    if (!empty($missingDocs)) {
        $versionsWithMissingDocs[] = [
            'version_id' => $version->id,
            'policy_id' => $version->policy_id,
            'customer_name' => $version->customer_name,
            'version_number' => $version->version_number,
            'missing_docs' => $missingDocs
        ];
    }
}

if (empty($versionsWithMissingDocs)) {
    echo "✅ All policy version documents are found on disk.\n\n";
} else {
    echo "❌ Found " . count($versionsWithMissingDocs) . " policy versions with missing documents:\n\n";
    
    foreach ($versionsWithMissingDocs as $versionData) {
        echo "Version ID: {$versionData['version_id']}\n";
        echo "Policy ID: {$versionData['policy_id']}\n";
        echo "Customer: {$versionData['customer_name']}\n";
        echo "Version: {$versionData['version_number']}\n";
        echo "Missing Documents:\n";
        
        foreach ($versionData['missing_docs'] as $doc) {
            echo "  - {$doc['type']}: {$doc['path']}\n";
            echo "    Expected at: {$doc['full_path']}\n";
        }
        echo "\n";
    }
}

// Storage directory analysis
echo "3. Storage directory analysis...\n";
$storagePath = storage_path('app');
echo "Storage path: {$storagePath}\n";

if (is_dir($storagePath)) {
    echo "✅ Storage directory exists\n";
    
    $privatePath = $storagePath . '/private';
    if (is_dir($privatePath)) {
        echo "✅ Private directory exists\n";
        
        $policiesPath = $privatePath . '/policies';
        if (is_dir($policiesPath)) {
            echo "✅ Policies directory exists\n";
            echo "Policies directories: " . count(glob($policiesPath . '/*', GLOB_ONLYDIR)) . "\n";
        } else {
            echo "❌ Policies directory missing\n";
        }
    } else {
        echo "❌ Private directory missing\n";
    }
} else {
    echo "❌ Storage directory missing\n";
}

// Recommendations
echo "\n=== Recommendations ===\n";

if (!empty($policiesWithMissingDocs) || !empty($versionsWithMissingDocs)) {
    echo "1. The following documents are missing from the server:\n";
    echo "   - This usually happens when the database was migrated but files weren't copied\n";
    echo "   - Or when files were deleted/moved after database migration\n\n";
    
    echo "2. Solutions:\n";
    echo "   a) Restore missing files from backup\n";
    echo "   b) Re-upload missing documents through the admin panel\n";
    echo "   c) Clean up database records for permanently missing files\n\n";
    
    echo "3. To clean up missing file references, run:\n";
    echo "   php cleanup_missing_documents.php\n\n";
}

echo "4. For better error handling, the download methods have been updated to:\n";
echo "   - Try multiple storage paths\n";
echo "   - Provide detailed error messages\n";
echo "   - Log missing file attempts\n\n";

echo "=== Diagnostic Complete ===\n";
