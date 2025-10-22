<?php

/**
 * Debug Version Creation Script
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;

echo "=== Debug Version Creation ===\n\n";

// 1. Test PolicyVersion model directly
echo "1. Testing PolicyVersion model...\n";
try {
    $testPolicy = Policy::first();
    if ($testPolicy) {
        echo "✅ Found test policy: {$testPolicy->customer_name}\n";
        
        $version = PolicyVersion::createFromPolicy(
            $testPolicy,
            'Test version creation',
            'Debug Script'
        );
        
        echo "✅ Version created: ID {$version->id}, Version {$version->version_number}\n";
        echo "✅ Version documents:\n";
        echo "  - Policy: {$version->policy_copy_path}\n";
        echo "  - RC: {$version->rc_copy_path}\n";
        echo "  - Aadhar: {$version->aadhar_copy_path}\n";
        echo "  - PAN: {$version->pan_copy_path}\n";
        
        // Clean up
        $version->delete();
        echo "✅ Test version cleaned up\n";
    } else {
        echo "❌ No policies found to test with\n";
    }
} catch (Exception $e) {
    echo "❌ Error creating version: " . $e->getMessage() . "\n";
}

// 2. Test version counting
echo "\n2. Testing version counting...\n";
try {
    $testPolicy = Policy::first();
    if ($testPolicy) {
        $nextVersion = PolicyVersion::getNextVersionNumber($testPolicy->id);
        echo "✅ Next version number for policy {$testPolicy->id}: {$nextVersion}\n";
        
        $existingVersions = PolicyVersion::where('policy_id', $testPolicy->id)->count();
        echo "✅ Existing versions for policy {$testPolicy->id}: {$existingVersions}\n";
    }
} catch (Exception $e) {
    echo "❌ Error with version counting: " . $e->getMessage() . "\n";
}

// 3. Test manual version creation
echo "\n3. Testing manual version creation...\n";
try {
    $testPolicy = Policy::first();
    if ($testPolicy) {
        $version = new PolicyVersion();
        $version->policy_id = $testPolicy->id;
        $version->version_number = 1;
        $version->customer_name = $testPolicy->customer_name;
        $version->policy_copy_path = $testPolicy->policy_copy_path;
        $version->notes = 'Manual test version';
        $version->created_by = 'Debug Script';
        $version->version_created_at = now();
        $version->save();
        
        echo "✅ Manual version created: ID {$version->id}\n";
        
        // Clean up
        $version->delete();
        echo "✅ Manual version cleaned up\n";
    }
} catch (Exception $e) {
    echo "❌ Error with manual version creation: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
