<?php

/**
 * CSRF Issue Fix Script
 * 
 * This script addresses the 419 Page Expired error by ensuring proper session and CSRF configuration
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

echo "=== CSRF Issue Fix Script ===\n\n";

// 1. Clear all caches
echo "1. Clearing all caches...\n";
try {
    Artisan::call('config:clear');
    echo "✅ Config cache cleared\n";
    
    Artisan::call('cache:clear');
    echo "✅ Application cache cleared\n";
    
    Artisan::call('route:clear');
    echo "✅ Route cache cleared\n";
    
    Artisan::call('view:clear');
    echo "✅ View cache cleared\n";
    
    Artisan::call('session:table');
    echo "✅ Session table ensured\n";
} catch (Exception $e) {
    echo "❌ Cache clearing failed: " . $e->getMessage() . "\n";
}

// 2. Check session configuration
echo "\n2. Checking session configuration...\n";
$sessionDriver = env('SESSION_DRIVER', 'file');
$sessionLifetime = env('SESSION_LIFETIME', 120);
echo "Session Driver: {$sessionDriver}\n";
echo "Session Lifetime: {$sessionLifetime} minutes\n";

// 3. Ensure session storage directory exists and has proper permissions
echo "\n3. Checking session storage...\n";
$sessionPath = storage_path('framework/sessions');
if (!File::exists($sessionPath)) {
    File::makeDirectory($sessionPath, 0755, true);
    echo "✅ Session directory created\n";
} else {
    echo "✅ Session directory exists\n";
}

// Set proper permissions
chmod($sessionPath, 0755);
echo "✅ Session directory permissions set\n";

// 4. Test session functionality
echo "\n4. Testing session functionality...\n";
try {
    session()->start();
    session()->put('test_csrf_fix', 'working');
    $value = session()->get('test_csrf_fix');
    if ($value === 'working') {
        echo "✅ Session read/write working\n";
    } else {
        echo "❌ Session read/write failed\n";
    }
} catch (Exception $e) {
    echo "❌ Session test failed: " . $e->getMessage() . "\n";
}

// 5. Test CSRF token generation
echo "\n5. Testing CSRF token generation...\n";
try {
    $token = csrf_token();
    echo "✅ CSRF token generated: " . substr($token, 0, 20) . "...\n";
} catch (Exception $e) {
    echo "❌ CSRF token generation failed: " . $e->getMessage() . "\n";
}

// 6. Check if we need to create a simple .htaccess for session handling
echo "\n6. Checking web server configuration...\n";
$publicPath = public_path();
$htaccessPath = $publicPath . '/.htaccess';

if (File::exists($htaccessPath)) {
    echo "✅ .htaccess file exists\n";
} else {
    echo "⚠️  .htaccess file not found - this might be needed for proper session handling\n";
}

echo "\n=== Fix Complete ===\n";
echo "If you're still getting 419 errors, try:\n";
echo "1. Restart your development server\n";
echo "2. Clear browser cache and cookies completely\n";
echo "3. Try the test login page: http://127.0.0.1:8000/test-login\n";
echo "4. Check if you have any browser extensions blocking cookies\n";
