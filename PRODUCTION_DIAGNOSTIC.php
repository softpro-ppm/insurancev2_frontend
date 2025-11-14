<?php

/**
 * Production Diagnostic Script
 * 
 * Upload this file to your production server and run it to diagnose issues
 */

echo "=== Production Diagnostic Script ===\n\n";

// 1. Check if files were updated
echo "1. Checking if files were updated...\n";

$policyControllerPath = 'app/Http/Controllers/PolicyController.php';
if (file_exists($policyControllerPath)) {
    $content = file_get_contents($policyControllerPath);
    $lastModified = date('Y-m-d H:i:s', filemtime($policyControllerPath));
    echo "✅ PolicyController.php exists (Last modified: {$lastModified})\n";
    
    // Check for our fixes
    if (strpos($content, 'base_path(\'public/\' . $filePath)') !== false) {
        echo "✅ Production paths fix found\n";
    } else {
        echo "❌ Production paths fix NOT found\n";
    }
    
    if (strpos($content, 'return redirect($fullPath)') !== false) {
        echo "✅ Remote URL redirect fix found\n";
    } else {
        echo "❌ Remote URL redirect fix NOT found\n";
    }
} else {
    echo "❌ PolicyController.php not found\n";
}

// 2. Check database connection
echo "\n2. Checking database connection...\n";
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "✅ Database connection successful\n";
    
    // Check if policies exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM policies");
    $result = $stmt->fetch();
    echo "✅ Found {$result['count']} policies in database\n";
    
    // Check policy document paths
    $stmt = $pdo->query("SELECT id, customer_name, policy_copy_path FROM policies LIMIT 3");
    $policies = $stmt->fetchAll();
    echo "\nSample policy document paths:\n";
    foreach ($policies as $policy) {
        echo "Policy {$policy['id']} ({$policy['customer_name']}): {$policy['policy_copy_path']}\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// 3. Check file paths
echo "\n3. Checking file paths...\n";
$testPaths = [
    'storage/app/',
    'public/storage/',
    'public/uploads/',
    'storage/'
];

foreach ($testPaths as $path) {
    if (is_dir($path)) {
        echo "✅ Directory exists: {$path}\n";
        $files = glob($path . '*');
        echo "   Files found: " . count($files) . "\n";
    } else {
        echo "❌ Directory missing: {$path}\n";
    }
}

// 4. Test document download logic
echo "\n4. Testing document download logic...\n";
if (isset($policies[0])) {
    $testPolicy = $policies[0];
    $filePath = $testPolicy['policy_copy_path'];
    
    if ($filePath) {
        echo "Testing file path: {$filePath}\n";
        
        // Check if it's a URL
        if (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://')) {
            echo "✅ File path is a URL - will redirect to: {$filePath}\n";
        } else {
            // Check local file paths
            $possiblePaths = [
                'storage/app/' . $filePath,
                'public/storage/' . $filePath,
                'public/uploads/' . $filePath,
                'public/' . $filePath,
                $filePath
            ];
            
            $found = false;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    echo "✅ File found at: {$path}\n";
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                echo "❌ File not found in any expected location\n";
                echo "This is why you're getting 'Document Not Available'\n";
            }
        }
    } else {
        echo "❌ No document path found for this policy\n";
    }
}

// 5. Check Laravel configuration
echo "\n5. Checking Laravel configuration...\n";
if (file_exists('.env')) {
    echo "✅ .env file exists\n";
    $env = file_get_contents('.env');
    if (strpos($env, 'APP_ENV=production') !== false) {
        echo "✅ Running in production mode\n";
    } else {
        echo "⚠️ Not running in production mode\n";
    }
} else {
    echo "❌ .env file not found\n";
}

echo "\n=== Diagnostic Complete ===\n";
echo "Upload this file to your production server and run it to see the results.\n";
echo "This will help identify what's causing the issue.\n";

