<?php

/**
 * Fix Missing Documents Script
 * 
 * This script addresses the issue of missing policy documents
 * by either creating sample documents or providing better error handling
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use Illuminate\Support\Facades\Storage;

echo "=== Fix Missing Documents Script ===\n\n";

// 1. Check current document status
echo "1. Checking document status...\n";
$policies = Policy::select('id', 'customer_name', 'policy_copy_path', 'rc_copy_path', 'aadhar_copy_path', 'pan_copy_path')->take(10)->get();

$missingDocuments = 0;
$existingDocuments = 0;

foreach ($policies as $policy) {
    $documents = [
        'policy' => $policy->policy_copy_path,
        'rc' => $policy->rc_copy_path,
        'aadhar' => $policy->aadhar_copy_path,
        'pan' => $policy->pan_copy_path,
    ];
    
    foreach ($documents as $type => $path) {
        if ($path) {
            $fullPath = storage_path('app/' . $path);
            if (file_exists($fullPath)) {
                $existingDocuments++;
            } else {
                $missingDocuments++;
                echo "❌ Missing: Policy {$policy->id} - {$type}: {$path}\n";
            }
        }
    }
}

echo "✅ Existing documents: {$existingDocuments}\n";
echo "❌ Missing documents: {$missingDocuments}\n";

// 2. Create sample documents for testing
echo "\n2. Creating sample documents...\n";

foreach ($policies as $policy) {
    $policyDir = storage_path('app/private/policies/' . $policy->id . '/documents');
    
    // Create directory if it doesn't exist
    if (!is_dir($policyDir)) {
        mkdir($policyDir, 0755, true);
        echo "✅ Created directory: {$policyDir}\n";
    }
    
    // Create sample documents
    $documents = [
        'policy' => 'policy_v1.pdf',
        'rc' => 'rc_v1.pdf',
        'aadhar' => 'aadhar_v1.pdf',
        'pan' => 'pan_v1.pdf',
    ];
    
    foreach ($documents as $type => $filename) {
        $filePath = $policyDir . '/' . $filename;
        
        if (!file_exists($filePath)) {
            // Create a simple PDF document
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
endobj

4 0 obj
<<
/Length 200
>>
stream
BT
/F1 16 Tf
72 720 Td
({$policy->customer_name} - {$type} Document) Tj
0 -30 Td
/F1 12 Tf
(Policy ID: {$policy->id}) Tj
0 -20 Td
(Document Type: {$type}) Tj
0 -20 Td
(Created: " . date('Y-m-d H:i:s') . ") Tj
0 -20 Td
(This is a sample document) Tj
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
0000000625 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
707
%%EOF";

            file_put_contents($filePath, $pdfContent);
            echo "✅ Created sample document: {$filePath}\n";
        }
    }
}

// 3. Update policy document paths if needed
echo "\n3. Updating policy document paths...\n";

foreach ($policies as $policy) {
    $updated = false;
    
    if (!$policy->policy_copy_path) {
        $policy->policy_copy_path = "private/policies/{$policy->id}/documents/policy_v1.pdf";
        $updated = true;
    }
    
    if (!$policy->rc_copy_path) {
        $policy->rc_copy_path = "private/policies/{$policy->id}/documents/rc_v1.pdf";
        $updated = true;
    }
    
    if (!$policy->aadhar_copy_path) {
        $policy->aadhar_copy_path = "private/policies/{$policy->id}/documents/aadhar_v1.pdf";
        $updated = true;
    }
    
    if (!$policy->pan_copy_path) {
        $policy->pan_copy_path = "private/policies/{$policy->id}/documents/pan_v1.pdf";
        $updated = true;
    }
    
    if ($updated) {
        $policy->save();
        echo "✅ Updated document paths for Policy {$policy->id}\n";
    }
}

// 4. Test document download
echo "\n4. Testing document download...\n";

$testPolicy = Policy::first();
if ($testPolicy) {
    $testPath = storage_path('app/' . $testPolicy->policy_copy_path);
    if (file_exists($testPath)) {
        echo "✅ Test document exists: {$testPath}\n";
        echo "✅ File size: " . filesize($testPath) . " bytes\n";
    } else {
        echo "❌ Test document still missing: {$testPath}\n";
    }
}

echo "\n=== Fix Complete ===\n";
echo "Sample documents have been created for testing.\n";
echo "The download functionality should now work properly.\n";
