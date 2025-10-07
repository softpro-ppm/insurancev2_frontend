<?php
/**
 * EMERGENCY FIX for 500 Internal Server Error
 * Upload this file to your live server and run it to fix the error
 */

echo "🚨 EMERGENCY FIX for 500 Internal Server Error\n";
echo "==============================================\n\n";

// 1. Clear all caches
echo "1. Clearing all caches...\n";
try {
    if (file_exists('artisan')) {
        exec('php artisan cache:clear 2>/dev/null');
        exec('php artisan config:clear 2>/dev/null');
        exec('php artisan route:clear 2>/dev/null');
        exec('php artisan view:clear 2>/dev/null');
        echo "✅ Laravel caches cleared\n";
    } else {
        echo "⚠️  Artisan not found, skipping Laravel cache clear\n";
    }
} catch (Exception $e) {
    echo "⚠️  Could not clear Laravel caches: " . $e->getMessage() . "\n";
}

// 2. Clear PHP opcache if available
echo "2. Clearing PHP opcache...\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ PHP opcache cleared\n";
} else {
    echo "⚠️  PHP opcache not available\n";
}

// 3. Check .env file
echo "3. Checking .env file...\n";
if (file_exists('.env')) {
    echo "✅ .env file exists\n";
    $envContent = file_get_contents('.env');
    if (strpos($envContent, 'APP_KEY=') !== false) {
        echo "✅ APP_KEY is set\n";
    } else {
        echo "❌ APP_KEY is missing - this could cause 500 errors\n";
        echo "   Please add: APP_KEY=base64:ZcluVpE3zyA3myeyjGI7Il2ne22PwkITV0Y7mX+YmNI=\n";
    }
} else {
    echo "❌ .env file missing - this will cause 500 errors\n";
    echo "   Creating basic .env file...\n";
    
    $basicEnv = 'APP_NAME="Insurance MS 2.0"
APP_ENV=production
APP_KEY=base64:ZcluVpE3zyA3myeyjGI7Il2ne22PwkITV0Y7mX+YmNI=
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

BROADCAST_DRIVER=log
CACHE_STORE=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"';
    
    file_put_contents('.env', $basicEnv);
    echo "✅ Basic .env file created\n";
}

// 4. Check file permissions
echo "4. Checking file permissions...\n";
$importantFiles = [
    '.env' => 644,
    'storage' => 755,
    'bootstrap/cache' => 755,
    'database/database.sqlite' => 644
];

foreach ($importantFiles as $file => $expectedPerms) {
    if (file_exists($file)) {
        $currentPerms = substr(sprintf('%o', fileperms($file)), -3);
        echo "   $file: $currentPerms (expected: $expectedPerms)\n";
    } else {
        echo "   $file: NOT FOUND\n";
    }
}

// 5. Test basic PHP functionality
echo "5. Testing basic PHP functionality...\n";
try {
    $test = new PDO('sqlite:database/database.sqlite');
    echo "✅ Database connection works\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// 6. Check for common error causes
echo "6. Checking for common error causes...\n";
$errorChecks = [
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size')
];

foreach ($errorChecks as $setting => $value) {
    echo "   $setting: $value\n";
}

echo "\n🎯 EMERGENCY FIX COMPLETE!\n";
echo "==========================\n\n";
echo "If you're still getting 500 errors:\n";
echo "1. Check your server error logs\n";
echo "2. Make sure .env file has APP_KEY\n";
echo "3. Verify file permissions are correct\n";
echo "4. Contact your hosting provider\n\n";
echo "Your site should now work! 🚀\n";
?>
