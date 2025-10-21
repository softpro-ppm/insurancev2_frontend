<?php

/**
 * Production File Storage Fix Script
 * 
 * This script helps fix missing document files on the production server.
 * Run this on the production server to handle missing files gracefully.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;

echo "=== Production File Storage Fix ===\n\n";

// Check specific policy mentioned in error
$policyId = 1436;
echo "Checking Policy ID: {$policyId}\n";

$policy = Policy::find($policyId);
if (!$policy) {
    echo "❌ Policy {$policyId} not found in database.\n";
    exit(1);
}

echo "✅ Policy found: {$policy->customer_name} - {$policy->vehicle_number}\n\n";

// Check document paths for this policy
$documentFields = [
    'policy_copy_path' => 'Policy Document',
    'rc_copy_path' => 'RC Document',
    'aadhar_copy_path' => 'Aadhar Document',
    'pan_copy_path' => 'PAN Document'
];

$missingDocs = [];
$existingDocs = [];

foreach ($documentFields as $field => $label) {
    if ($policy->$field) {
        $fullPath = storage_path('app/' . $policy->$field);
        if (file_exists($fullPath)) {
            $existingDocs[] = [
                'type' => $label,
                'path' => $policy->$field,
                'size' => filesize($fullPath),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath))
            ];
            echo "✅ {$label}: Found at {$policy->$field}\n";
        } else {
            $missingDocs[] = [
                'type' => $label,
                'path' => $policy->$field,
                'full_path' => $fullPath
            ];
            echo "❌ {$label}: Missing - {$policy->$field}\n";
        }
    } else {
        echo "⚪ {$label}: No file path in database\n";
    }
}

echo "\n";

// Check storage directory structure
echo "Storage directory analysis:\n";
$storagePath = storage_path('app');
echo "Storage path: {$storagePath}\n";

if (!is_dir($storagePath)) {
    echo "❌ Storage directory does not exist!\n";
    echo "Creating storage directory structure...\n";
    
    $directories = [
        $storagePath,
        $storagePath . '/private',
        $storagePath . '/private/policies',
        $storagePath . '/private/policies/' . $policyId,
        $storagePath . '/private/policies/' . $policyId . '/documents'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "Created: {$dir}\n";
        }
    }
} else {
    echo "✅ Storage directory exists\n";
    
    // Check specific policy directory
    $policyDir = $storagePath . '/private/policies/' . $policyId;
    if (!is_dir($policyDir)) {
        echo "❌ Policy directory missing: {$policyDir}\n";
        echo "Creating policy directory...\n";
        mkdir($policyDir, 0755, true);
        mkdir($policyDir . '/documents', 0755, true);
        echo "✅ Created policy directory structure\n";
    } else {
        echo "✅ Policy directory exists: {$policyDir}\n";
        
        $documentsDir = $policyDir . '/documents';
        if (!is_dir($documentsDir)) {
            echo "❌ Documents directory missing: {$documentsDir}\n";
            mkdir($documentsDir, 0755, true);
            echo "✅ Created documents directory\n";
        } else {
            echo "✅ Documents directory exists: {$documentsDir}\n";
            
            // List existing files in documents directory
            $files = glob($documentsDir . '/*');
            if (empty($files)) {
                echo "📁 Documents directory is empty\n";
            } else {
                echo "📁 Files in documents directory:\n";
                foreach ($files as $file) {
                    echo "  - " . basename($file) . " (" . filesize($file) . " bytes)\n";
                }
            }
        }
    }
}

echo "\n=== Solutions ===\n";

if (!empty($missingDocs)) {
    echo "1. Missing Documents Detected:\n";
    foreach ($missingDocs as $doc) {
        echo "   - {$doc['type']}: {$doc['path']}\n";
    }
    
    echo "\n2. Recommended Actions:\n";
    echo "   a) Check if files exist in backup or another location\n";
    echo "   b) Re-upload missing documents through admin panel\n";
    echo "   c) Remove database references to missing files\n";
    
    echo "\n3. To remove missing file references, run:\n";
    echo "   php cleanup_missing_documents.php\n";
    
    echo "\n4. To create placeholder files (temporary solution):\n";
    echo "   php create_placeholder_documents.php\n";
} else {
    echo "✅ All documents are present!\n";
}

echo "\n=== Fix Complete ===\n";
