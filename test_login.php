<?php

/**
 * Login Test Script
 * 
 * This script tests the login functionality and CSRF token generation
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Login Test Script ===\n\n";

// Check if admin user exists
$adminUser = User::where('email', 'admin@insurance.com')->first();

if (!$adminUser) {
    echo "Creating admin user...\n";
    $adminUser = User::create([
        'name' => 'Admin',
        'email' => 'admin@insurance.com',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
    ]);
    echo "✅ Admin user created\n\n";
} else {
    echo "✅ Admin user exists\n";
    echo "Email: {$adminUser->email}\n";
    echo "Name: {$adminUser->name}\n\n";
}

// Test CSRF token generation
echo "Testing CSRF token generation...\n";
try {
    $csrfToken = csrf_token();
    echo "✅ CSRF token generated: " . substr($csrfToken, 0, 20) . "...\n\n";
} catch (Exception $e) {
    echo "❌ CSRF token generation failed: " . $e->getMessage() . "\n\n";
}

// Test session functionality
echo "Testing session functionality...\n";
try {
    session()->put('test_key', 'test_value');
    $sessionValue = session()->get('test_key');
    if ($sessionValue === 'test_value') {
        echo "✅ Session functionality working\n\n";
    } else {
        echo "❌ Session functionality not working\n\n";
    }
} catch (Exception $e) {
    echo "❌ Session test failed: " . $e->getMessage() . "\n\n";
}

// Test login route
echo "Testing login route...\n";
try {
    $loginRoute = route('login');
    echo "✅ Login route: {$loginRoute}\n\n";
} catch (Exception $e) {
    echo "❌ Login route test failed: " . $e->getMessage() . "\n\n";
}

echo "=== Login Test Complete ===\n";
echo "If all tests passed, the login should work properly.\n";
echo "If you're still getting 419 errors, try:\n";
echo "1. Clear browser cache and cookies\n";
echo "2. Try incognito/private browsing mode\n";
echo "3. Restart your development server\n";
