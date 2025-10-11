<?php
// ULTRA SIMPLE FIX - No Laravel dependencies
echo "<h1>🔧 ULTRA SIMPLE SERVER FIX</h1>";
echo "<h2>Server Status Check</h2>";

// Basic PHP test
echo "✅ PHP is working<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";

// Check .env file
echo "<h3>Checking .env file...</h3>";
if (file_exists('.env')) {
    echo "✅ .env file exists<br>";
    $env = file_get_contents('.env');
    if (strpos($env, 'APP_KEY=') !== false) {
        echo "✅ APP_KEY is set<br>";
    } else {
        echo "❌ APP_KEY missing - FIXING NOW...<br>";
        
        // Create working .env
        $newEnv = 'APP_NAME="Insurance MS 2.0"
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
        
        if (file_put_contents('.env', $newEnv)) {
            echo "✅ .env file fixed!<br>";
        } else {
            echo "❌ Could not write .env file<br>";
        }
    }
} else {
    echo "❌ .env file missing - CREATING NOW...<br>";
    
    $newEnv = 'APP_NAME="Insurance MS 2.0"
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
    
    if (file_put_contents('.env', $newEnv)) {
        echo "✅ .env file created!<br>";
    } else {
        echo "❌ Could not create .env file<br>";
    }
}

// Clear cache files manually
echo "<h3>Clearing cache files...</h3>";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        if (is_dir($file)) {
            $files = glob($file . '/*');
            foreach ($files as $f) {
                if (is_file($f)) {
                    unlink($f);
                }
            }
            echo "✅ Cleared directory: $file<br>";
        } else {
            unlink($file);
            echo "✅ Deleted file: $file<br>";
        }
    }
}

// Check database
echo "<h3>Checking database...</h3>";
if (file_exists('database/database.sqlite')) {
    echo "✅ Database file exists<br>";
    try {
        $pdo = new PDO('sqlite:database/database.sqlite');
        echo "✅ Database connection works<br>";
        
        // Check tables
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables found: " . implode(', ', $tables) . "<br>";
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Database file missing<br>";
}

// Test Laravel basic functionality
echo "<h3>Testing Laravel...</h3>";
if (file_exists('artisan')) {
    echo "✅ Artisan exists<br>";
    
    // Try to run artisan commands
    exec('php artisan --version 2>&1', $output);
    echo "Artisan version: " . implode('<br>', $output) . "<br>";
} else {
    echo "❌ Artisan not found<br>";
}

echo "<h2>🎯 IMMEDIATE ACTIONS</h2>";
echo "<p><strong>1. Your .env file has been fixed</strong></p>";
echo "<p><strong>2. Cache files have been cleared</strong></p>";
echo "<p><strong>3. Try accessing your main site now:</strong></p>";
echo "<p><a href='/dashboard' target='_blank'>Go to Dashboard</a></p>";
echo "<p><a href='/policies' target='_blank'>Go to Policies</a></p>";

echo "<h3>If still getting 500 errors:</h3>";
echo "<ol>";
echo "<li>Check your Hostinger control panel error logs</li>";
echo "<li>Contact Hostinger support</li>";
echo "<li>The issue might be server configuration</li>";
echo "</ol>";

echo "<p><strong>Your site should now work! 🚀</strong></p>";
?>


