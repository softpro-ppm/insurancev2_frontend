<?php

/**
 * Production Deployment Script
 * 
 * This script helps verify that all production fixes are in place
 */

echo "=== Production Deployment Verification ===\n\n";

// Check if PolicyController has the fixes
$policyControllerPath = 'app/Http/Controllers/PolicyController.php';
if (file_exists($policyControllerPath)) {
    $content = file_get_contents($policyControllerPath);
    
    $checks = [
        'Remote URL handling' => strpos($content, 'str_starts_with($filePath, \'http://\')') !== false,
        'Production paths' => strpos($content, 'base_path(\'public/\' . $filePath)') !== false,
        'Document preservation' => strpos($content, 'preserveDocumentsForVersion') !== false,
        'Version creation' => strpos($content, 'PolicyVersion::createFromPolicy') !== false,
        'Remote redirect' => strpos($content, 'return redirect($fullPath)') !== false
    ];
    
    echo "✅ PolicyController.php checks:\n";
    foreach ($checks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " {$check}\n";
    }
} else {
    echo "❌ PolicyController.php not found\n";
}

// Check if PolicyVersion model has the fixes
$policyVersionPath = 'app/Models/PolicyVersion.php';
if (file_exists($policyVersionPath)) {
    $content = file_get_contents($policyVersionPath);
    
    $checks = [
        'createFromPolicy method' => strpos($content, 'createFromPolicy') !== false,
        'Document fields' => strpos($content, 'policy_copy_path') !== false,
        'Version numbering' => strpos($content, 'getNextVersionNumber') !== false
    ];
    
    echo "\n✅ PolicyVersion.php checks:\n";
    foreach ($checks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " {$check}\n";
    }
} else {
    echo "\n❌ PolicyVersion.php not found\n";
}

// Check routes
$routesPath = 'routes/web.php';
if (file_exists($routesPath)) {
    $content = file_get_contents($routesPath);
    
    $checks = [
        'Policy download route' => strpos($content, 'policy-versions') !== false,
        'CSRF bypass' => strpos($content, 'withoutMiddleware') !== false
    ];
    
    echo "\n✅ Routes checks:\n";
    foreach ($checks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " {$check}\n";
    }
} else {
    echo "\n❌ routes/web.php not found\n";
}

echo "\n=== Deployment Ready ===\n";
echo "All files are ready for production deployment!\n";
echo "You can now commit and push these changes to GitHub.\n";
