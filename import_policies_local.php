<?php
/**
 * Import Production Policies to Local Database
 * Run this locally: php import_policies_local.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "====================================\n";
echo "Policy Import Tool - Production to Local\n";
echo "====================================\n\n";

// Check for JSON file
$jsonFile = __DIR__ . '/policies_export.json';

if (!file_exists($jsonFile)) {
    echo "❌ Error: policies_export.json not found!\n";
    echo "Please download the export file from production and save it as:\n";
    echo "{$jsonFile}\n";
    exit(1);
}

echo "📂 Reading export file...\n";
$json = file_get_contents($jsonFile);
$data = json_decode($json, true);

if (!$data || !isset($data['policies'])) {
    echo "❌ Error: Invalid JSON format!\n";
    exit(1);
}

$agents = $data['agents'] ?? [];
$policies = $data['policies'];
$totalAgents = count($agents);
$totalPolicies = count($policies);

echo "✅ Found {$totalAgents} agents to import\n";
echo "✅ Found {$totalPolicies} policies to import\n";
echo "📅 Exported at: {$data['exported_at']}\n\n";

// Ask for confirmation
echo "⚠️  WARNING: This will:\n";
echo "   1. DELETE all existing local agents\n";
echo "   2. DELETE all existing local policies and versions\n";
echo "   3. IMPORT {$totalAgents} agents from production\n";
echo "   4. IMPORT {$totalPolicies} policies from production\n\n";
echo "Do you want to continue? (yes/no): ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    echo "❌ Import cancelled.\n";
    exit(0);
}

echo "\n🗑️  Clearing local database...\n";

try {
    // Clear existing data
    $deletedAgents = \DB::table('agents')->count();
    $deletedPolicies = \App\Models\Policy::count();
    $deletedVersions = \DB::table('policy_versions')->count();
    
    \DB::table('agents')->truncate();
    \App\Models\Policy::truncate();
    \DB::table('policy_versions')->truncate();
    
    echo "   ✅ Deleted {$deletedAgents} agents, {$deletedPolicies} policies, and {$deletedVersions} versions\n\n";
    
    // Import agents first
    echo "📥 Importing agents...\n";
    $agentsImported = 0;
    foreach ($agents as $agentData) {
        try {
            \DB::table('agents')->insert($agentData);
            $agentsImported++;
        } catch (\Exception $e) {
            echo "   ⚠️  Error importing agent: " . $e->getMessage() . "\n";
        }
    }
    echo "   ✅ Imported {$agentsImported}/{$totalAgents} agents\n\n";
    
    echo "📥 Importing policies...\n";
    
    $imported = 0;
    $errors = 0;
    $versionsImported = 0;
    
    foreach ($policies as $index => $policyData) {
        try {
            // Extract versions before creating policy
            $versions = $policyData['versions'] ?? [];
            unset($policyData['versions']);
            
            // Create policy with original timestamps (using DB::table to preserve timestamps)
            $policyId = \DB::table('policies')->insertGetId($policyData);
            
            // Import policy versions if any
            if (!empty($versions)) {
                foreach ($versions as $versionData) {
                    // Update policy_id to match the new local ID
                    $versionData['policy_id'] = $policyId;
                    \DB::table('policy_versions')->insert($versionData);
                    $versionsImported++;
                }
            }
            
            $imported++;
            
            // Progress indicator
            if (($imported % 50) == 0) {
                echo "   Progress: {$imported}/{$totalPolicies} policies...\n";
            }
            
        } catch (\Exception $e) {
            $errors++;
            echo "   ⚠️  Error importing policy #{$index}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Import completed!\n\n";
    echo "====================================\n";
    echo "IMPORT SUMMARY\n";
    echo "====================================\n";
    echo "Agents imported: {$agentsImported}/{$totalAgents}\n";
    echo "Policies imported: {$imported}/{$totalPolicies}\n";
    echo "Policy versions imported: {$versionsImported}\n";
    echo "Policy errors: {$errors}\n";
    echo "====================================\n\n";
    
    // Verify
    echo "🔍 Verification:\n";
    $localAgentsCount = \DB::table('agents')->count();
    $localCount = \App\Models\Policy::count();
    $localVersionsCount = \DB::table('policy_versions')->count();
    echo "   Agents in local DB: {$localAgentsCount}\n";
    echo "   Policies in local DB: {$localCount}\n";
    echo "   Versions in local DB: {$localVersionsCount}\n\n";
    
    if ($localCount == $totalPolicies && $localAgentsCount == $totalAgents) {
        echo "✅ SUCCESS! All agents, policies, and versions imported correctly.\n";
    } else {
        echo "⚠️  WARNING: Data mismatch detected.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

