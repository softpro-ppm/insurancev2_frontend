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

// Use the same resolution logic as the PolicyController so paths match your current storage setup
/** @var \App\Http\Controllers\PolicyController $controller */
$controller = app(\App\Models\Policy::class)->getConnection()->getPdo()
    ? app(\App\Http\Controllers\PolicyController::class)
    : null;

echo "1. Checking policies with missing documents...\n";
$policiesWithMissingDocs = [];

$policies = Policy::where(function ($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($policies as $policy) {
    $missingDocs = [];
    $documentFields = [
        'policy_copy_path' => 'Policy Document',
        'rc_copy_path'     => 'RC Document',
        'aadhar_copy_path' => 'Aadhar Document',
        'pan_copy_path'    => 'PAN Document',
    ];

    foreach ($documentFields as $field => $label) {
        if ($policy->$field) {
            $fullPath = null;

            if ($controller) {
                // resolveDocumentPath is private, so we call it via a bound Closure
                $resolver = function ($path) {
                    return $this->{"resolveDocumentPath"}($path);
                };
                $resolver = \Closure::bind($resolver, $controller, get_class($controller));
                [$fullPath, $isRemote] = $resolver($policy->{$field});
            }

            // Fallback to legacy behaviour if controller resolution fails
            if (!$fullPath) {
                $relative = ltrim($policy->{$field}, '/');
                $fullPath = storage_path('app/private/' . $relative);
                if (!file_exists($fullPath)) {
                    $alt = storage_path('app/' . $relative);
                    if (file_exists($alt)) {
                        $fullPath = $alt;
                    }
                }
            }

            if (!$fullPath || !file_exists($fullPath)) {
                $missingDocs[] = [
                    'type'      => $label,
                    'path'      => $policy->$field,
                    'full_path' => $fullPath ?: '(unresolved)',
                ];
            }
        }
    }

    if (!empty($missingDocs)) {
        $policiesWithMissingDocs[] = [
            'policy_id'      => $policy->id,
            'customer_name'  => $policy->customer_name,
            'vehicle_number' => $policy->vehicle_number,
            'missing_docs'   => $missingDocs,
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

        foreach ($doc as $policyData['missing_docs']) {
            echo "  - {$doc['type']}: {$doc['path']}\n";
            echo "    Expected at: {$doc['full_path']}\n";
        }
        echo "\n";
    }
}

// Check policy versions with missing documents
echo "2. Checking policy versions with missing documents...\n";
$versionsWithMissingDocs = [];

$versions = PolicyVersion::where(function ($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($versions as $version) {
    $missingDocs = [];
    $documentFields = [
        'policy_copy_path' => 'Policy Document',
        'rc_copy_path'     => 'RC Document',
        'aadhar_copy_path' => 'Aadhar Document',
        'pan_copy_path'    => 'PAN Document',
    ];

    foreach ($documentFields as $field => $label) {
        if ($version->$field) {
            $fullPath = null;

            if ($controller) {
                $resolver = function ($path) {
                    return $this->{"resolveDocumentPath"}($path);
                };
                $resolver = \Closure::bind($resolver, $controller, get_class($controller));
                [$fullPath, $isRemote] = $resolver($version->{$field});
            }

            if (!$fullPath) {
                $relative = ltrim($version->{$field}, '/');
                $fullPath = storage_path('app/private/' . $relative);
                if (!file_exists($fullPath)) {
                    $alt = storage_path('app/' . $relative);
                    if (file_exists($alt)) {
                        $fullPath = $alt;
                    }
                }
            }

            if (!$fullPath || !file_exists($fullPath)) {
                $missingDocs[] = [
                    'type'      => $label,
                    'path'      => $version->$field,
                    'full_path' => $fullPath ?: '(unresolved)',
                ];
            }
        }
    }

    if (!empty($missingDocs)) {
        $versionsWithMissingDocs[] = [
            'version_id'     => $version->id,
            'policy_id'      => $version->policy_id,
            'customer_name'  => $version->customer_name,
            'version_number' => $version->version_number,
            'missing_docs'   => $missingDocs,
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

        foreach ($doc as $versionData['missing_docs']) {
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

    $privateRoot = $storagePath . '/private';
    if (is_dir($privateRoot)) {
        echo "✅ Private directory exists (app/private)\n";

        // With the current config, local disk root is app/private and we store under private/policies
        $policiesPath = $privateRoot . '/private/policies';
        if (is_dir($policiesPath)) {
            echo "✅ Policies directory exists at {$policiesPath}\n";
            echo "Policies directories: " . count(glob($policiesPath . '/*', GLOB_ONLYDIR)) . "\n";
        } else {
            echo "❌ Policies directory (private/policies) missing under {$privateRoot}\n";
        }
    } else {
        echo "❌ Private root directory (app/private) missing\n";
    }
} else {
    echo "❌ Storage directory (app) missing\n";
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
