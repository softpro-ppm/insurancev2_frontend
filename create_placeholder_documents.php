<?php

/**
 * Create Placeholder Documents Script
 * 
 * This script creates placeholder PDF files for missing documents.
 * This is a temporary solution to prevent download errors.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;

echo "=== Create Placeholder Documents ===\n\n";

// Function to create a placeholder PDF
function createPlaceholderPDF($filePath, $customerName, $documentType) {
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Create a simple PDF content (minimal valid PDF)
    $pdfContent = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
/Resources <<
/Font <<
/F1 5 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 200
>>
stream
BT
/F1 12 Tf
72 720 Td
(Document Not Available) Tj
0 -20 Td
(Customer: {$customerName}) Tj
0 -20 Td
(Document Type: {$documentType}) Tj
0 -20 Td
(This is a placeholder document) Tj
ET
endstream
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000274 00000 n 
0000000525 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
607
%%EOF";

    return file_put_contents($filePath, $pdfContent) !== false;
}

// Check policies with missing documents
echo "1. Checking for missing documents...\n";
$fixedCount = 0;

$policies = Policy::where(function($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($policies as $policy) {
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
                echo "  Creating placeholder for Policy {$policy->id}, {$label}...\n";
                if (createPlaceholderPDF($fullPath, $policy->customer_name, $label)) {
                    echo "    ✅ Created: {$fullPath}\n";
                    $fixedCount++;
                } else {
                    echo "    ❌ Failed to create: {$fullPath}\n";
                }
            }
        }
    }
}

// Check policy versions with missing documents
echo "\n2. Checking policy versions for missing documents...\n";

$versions = PolicyVersion::where(function($query) {
    $query->whereNotNull('policy_copy_path')
          ->orWhereNotNull('rc_copy_path')
          ->orWhereNotNull('aadhar_copy_path')
          ->orWhereNotNull('pan_copy_path');
})->get();

foreach ($versions as $version) {
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
                echo "  Creating placeholder for Version {$version->id}, {$label}...\n";
                if (createPlaceholderPDF($fullPath, $version->customer_name, $label)) {
                    echo "    ✅ Created: {$fullPath}\n";
                    $fixedCount++;
                } else {
                    echo "    ❌ Failed to create: {$fullPath}\n";
                }
            }
        }
    }
}

echo "\n=== Summary ===\n";
echo "Total placeholder documents created: {$fixedCount}\n\n";

if ($fixedCount > 0) {
    echo "✅ Placeholder documents have been created.\n";
    echo "These will prevent download errors and show a 'Document Not Available' message.\n";
    echo "Replace these with actual documents when available.\n";
} else {
    echo "✅ No missing documents found. All files are present.\n";
}

echo "\n=== Complete ===\n";
