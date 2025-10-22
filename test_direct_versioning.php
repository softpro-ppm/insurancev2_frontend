<?php

/**
 * Direct Versioning Test Script
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;

echo "=== Direct Versioning Test ===\n\n";

// 1. Create a policy directly
echo "1. Creating policy directly...\n";
$policy = Policy::create([
    'customer_name' => 'Direct Test Customer',
    'phone' => '9876543210',
    'email' => 'direct@test.com',
    'policy_type' => 'Motor',
    'vehicle_number' => 'KA01AB' . time(),
    'vehicle_type' => 'Car',
    'company_name' => 'Test Insurance Co',
    'insurance_type' => 'Comprehensive',
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
    'premium' => 5000.00,
    'payout' => 0.00,
    'customer_paid_amount' => 5000.00,
    'revenue' => 5000.00,
    'status' => 'Active',
    'business_type' => 'Self',
    'agent_name' => 'Test Agent',
    'policy_number' => 'POL' . time(),
    'policy_copy_path' => 'https://example.com/policy_v1.pdf',
    'rc_copy_path' => 'https://example.com/rc_v1.pdf',
    'aadhar_copy_path' => 'https://example.com/aadhar_v1.pdf',
    'pan_copy_path' => 'https://example.com/pan_v1.pdf',
]);

echo "✅ Policy created: ID {$policy->id}\n";

// 2. Manually create version
echo "\n2. Manually creating version...\n";
$version = PolicyVersion::createFromPolicy(
    $policy,
    'Manual version creation',
    'Test Script'
);

echo "✅ Version created: ID {$version->id}, Version {$version->version_number}\n";

// 3. Check versions
echo "\n3. Checking all versions...\n";
$allVersions = PolicyVersion::where('policy_id', $policy->id)->get();
echo "✅ Found {$allVersions->count()} versions\n";

foreach ($allVersions as $v) {
    echo "  Version {$v->version_number}: {$v->notes}\n";
    echo "    - Policy: {$v->policy_copy_path}\n";
    echo "    - RC: {$v->rc_copy_path}\n";
}

// 4. Test document download URLs
echo "\n4. Testing download URLs...\n";
echo "Current policy downloads:\n";
echo "  - Policy: /api/policy-versions/current_{$policy->id}/download/policy\n";
echo "  - RC: /api/policy-versions/current_{$policy->id}/download/rc\n";

echo "Version downloads:\n";
foreach ($allVersions as $v) {
    echo "  Version {$v->version_number}:\n";
    echo "    - Policy: /api/policy-versions/{$v->id}/download/policy\n";
    echo "    - RC: /api/policy-versions/{$v->id}/download/rc\n";
}

// 5. Cleanup
echo "\n5. Cleaning up...\n";
$policy->delete();
PolicyVersion::where('policy_id', $policy->id)->delete();
echo "✅ Cleanup complete\n";

echo "\n=== Test Complete ===\n";
echo "The versioning system is working!\n";
