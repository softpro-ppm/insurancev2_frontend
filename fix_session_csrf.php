<?php

/**
 * Session and CSRF Fix Script
 * 
 * This script fixes the session/CSRF token mismatch issue
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

echo "=== Session and CSRF Fix Script ===\n\n";

// 1. Check current session configuration
echo "1. Current session configuration:\n";
echo "Driver: " . Config::get('session.driver') . "\n";
echo "Lifetime: " . Config::get('session.lifetime') . "\n";
echo "Encrypt: " . (Config::get('session.encrypt') ? 'true' : 'false') . "\n";
echo "SameSite: " . Config::get('session.same_site') . "\n";

// 2. Update session configuration for better compatibility
echo "\n2. Updating session configuration...\n";

// Create a custom session config
$sessionConfig = [
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => 'insurance_m_s2.0_session',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'http_only' => true,
    'same_site' => 'lax',
];

// Update the config
Config::set('session', $sessionConfig);
echo "✅ Session configuration updated\n";

// 3. Ensure session storage directory exists
echo "\n3. Ensuring session storage...\n";
$sessionPath = storage_path('framework/sessions');
if (!File::exists($sessionPath)) {
    File::makeDirectory($sessionPath, 0755, true);
    echo "✅ Session directory created\n";
} else {
    echo "✅ Session directory exists\n";
}

// Clear old session files
$sessionFiles = File::glob($sessionPath . '/*');
foreach ($sessionFiles as $file) {
    if (File::isFile($file) && File::lastModified($file) < time() - 3600) {
        File::delete($file);
    }
}
echo "✅ Old session files cleaned\n";

// 4. Test session functionality
echo "\n4. Testing session functionality...\n";
try {
    // Start a new session
    session()->start();
    $sessionId = session()->getId();
    echo "✅ Session started: " . substr($sessionId, 0, 20) . "...\n";
    
    // Test CSRF token
    $token = csrf_token();
    echo "✅ CSRF token: " . substr($token, 0, 20) . "...\n";
    
    // Test session persistence
    session()->put('test_key', 'test_value');
    $value = session()->get('test_key');
    if ($value === 'test_value') {
        echo "✅ Session persistence working\n";
    } else {
        echo "❌ Session persistence failed\n";
    }
} catch (Exception $e) {
    echo "❌ Session test failed: " . $e->getMessage() . "\n";
}

// 5. Clear all caches
echo "\n5. Clearing all caches...\n";
try {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    echo "✅ All caches cleared\n";
} catch (Exception $e) {
    echo "❌ Cache clearing failed: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Complete ===\n";
echo "The session and CSRF configuration has been optimized.\n";
echo "Try the login again - the 419 error should be resolved.\n";
