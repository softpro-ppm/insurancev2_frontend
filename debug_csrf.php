<?php

/**
 * CSRF Debug Script
 * 
 * This script helps debug CSRF token issues
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

echo "=== CSRF Debug Script ===\n\n";

// Test session start
echo "1. Testing session functionality...\n";
try {
    session()->start();
    echo "✅ Session started successfully\n";
    echo "Session ID: " . session()->getId() . "\n";
} catch (Exception $e) {
    echo "❌ Session start failed: " . $e->getMessage() . "\n";
}

// Test CSRF token generation
echo "\n2. Testing CSRF token generation...\n";
try {
    $token = csrf_token();
    echo "✅ CSRF token generated: " . substr($token, 0, 20) . "...\n";
    echo "Token length: " . strlen($token) . "\n";
} catch (Exception $e) {
    echo "❌ CSRF token generation failed: " . $e->getMessage() . "\n";
}

// Test session storage
echo "\n3. Testing session storage...\n";
try {
    session()->put('test_key', 'test_value');
    $value = session()->get('test_key');
    if ($value === 'test_value') {
        echo "✅ Session storage working\n";
    } else {
        echo "❌ Session storage not working\n";
    }
} catch (Exception $e) {
    echo "❌ Session storage failed: " . $e->getMessage() . "\n";
}

// Test route generation
echo "\n4. Testing route generation...\n";
try {
    $loginRoute = route('login');
    echo "✅ Login route: {$loginRoute}\n";
} catch (Exception $e) {
    echo "❌ Route generation failed: " . $e->getMessage() . "\n";
}

// Test middleware
echo "\n5. Testing middleware configuration...\n";
try {
    $middleware = app('Illuminate\Contracts\Http\Kernel');
    echo "✅ Kernel loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Kernel loading failed: " . $e->getMessage() . "\n";
}

// Test environment
echo "\n6. Environment check...\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";

echo "\n=== Debug Complete ===\n";
