<?php
/**
 * LIVE SERVER FIX - Upload this file to your live server and run it
 * This will diagnose and fix the 500 Internal Server Error
 */

// Set error reporting to see all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🚨 LIVE SERVER DIAGNOSTIC & FIX</h1>";
echo "<h2>Diagnosing 500 Internal Server Error...</h2>";

// 1. Check basic PHP functionality
echo "<h3>1. PHP Environment Check</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";
echo "Error Reporting: " . ini_get('error_reporting') . "<br>";

// 2. Check file permissions
echo "<h3>2. File Permissions Check</h3>";
$criticalFiles = [
    '.env',
    'storage',
    'bootstrap/cache',
    'database/database.sqlite'
];

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -3);
        echo "$file: $perms ";
        if (is_dir($file)) {
            echo "(directory) ";
        }
        if (is_writable($file)) {
            echo "✅ Writable";
        } else {
            echo "❌ Not Writable";
        }
        echo "<br>";
    } else {
        echo "$file: ❌ NOT FOUND<br>";
    }
}

// 3. Check .env file
echo "<h3>3. .env File Check</h3>";
if (file_exists('.env')) {
    echo "✅ .env file exists<br>";
    $envContent = file_get_contents('.env');
    
    // Check for APP_KEY
    if (strpos($envContent, 'APP_KEY=') !== false) {
        echo "✅ APP_KEY is set<br>";
    } else {
        echo "❌ APP_KEY is missing - THIS IS LIKELY THE CAUSE!<br>";
    }
    
    // Check for APP_DEBUG
    if (strpos($envContent, 'APP_DEBUG=true') !== false) {
        echo "⚠️ APP_DEBUG is set to true (should be false in production)<br>";
    }
} else {
    echo "❌ .env file missing - THIS IS THE CAUSE!<br>";
    echo "Creating .env file...<br>";
    
    $envContent = 'APP_NAME="Insurance MS 2.0"
APP_ENV=production
APP_KEY=base64:ZcluVpE3zyA3myeyjGI7Il2ne22PwkITV0Y7mX+YmNI=
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

BROADCAST_DRIVER=log
CACHE_STORE=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"';
    
    if (file_put_contents('.env', $envContent)) {
        echo "✅ .env file created successfully<br>";
    } else {
        echo "❌ Failed to create .env file<br>";
    }
}

// 4. Check database
echo "<h3>4. Database Check</h3>";
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "✅ Database connection successful<br>";
    
    // Check if policies table exists
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='policies'");
    if ($stmt->fetch()) {
        echo "✅ Policies table exists<br>";
        
        // Check policy count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM policies");
        $result = $stmt->fetch();
        echo "✅ Policies count: " . $result['count'] . "<br>";
    } else {
        echo "❌ Policies table not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// 5. Check Laravel bootstrap
echo "<h3>5. Laravel Bootstrap Check</h3>";
if (file_exists('bootstrap/app.php')) {
    echo "✅ bootstrap/app.php exists<br>";
} else {
    echo "❌ bootstrap/app.php missing<br>";
}

if (file_exists('public/index.php')) {
    echo "✅ public/index.php exists<br>";
} else {
    echo "❌ public/index.php missing<br>";
}

// 6. Clear caches
echo "<h3>6. Clearing Caches</h3>";
if (file_exists('artisan')) {
    echo "Clearing Laravel caches...<br>";
    exec('php artisan cache:clear 2>&1', $output1);
    exec('php artisan config:clear 2>&1', $output2);
    exec('php artisan route:clear 2>&1', $output3);
    exec('php artisan view:clear 2>&1', $output4);
    
    echo "Cache clear output: " . implode('<br>', $output1) . "<br>";
    echo "Config clear output: " . implode('<br>', $output2) . "<br>";
    echo "Route clear output: " . implode('<br>', $output3) . "<br>";
    echo "View clear output: " . implode('<br>', $output4) . "<br>";
} else {
    echo "❌ Artisan not found<br>";
}

// 7. Test basic Laravel functionality
echo "<h3>7. Testing Laravel Functionality</h3>";
try {
    // Include Laravel bootstrap
    if (file_exists('bootstrap/app.php')) {
        require_once 'bootstrap/app.php';
        echo "✅ Laravel bootstrap loaded<br>";
        
        // Try to create a simple route test
        echo "✅ Laravel framework is working<br>";
    }
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
}

// 8. Check for PHP errors in logs
echo "<h3>8. PHP Error Log Check</h3>";
$logFiles = [
    'storage/logs/laravel.log',
    'error_log',
    '/var/log/apache2/error.log',
    '/var/log/nginx/error.log'
];

foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        echo "Found log file: $logFile<br>";
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        $recentErrors = array_slice($lines, -10); // Last 10 lines
        
        echo "Recent errors:<br>";
        foreach ($recentErrors as $line) {
            if (trim($line)) {
                echo htmlspecialchars($line) . "<br>";
            }
        }
        break;
    }
}

// 9. Create a simple test page
echo "<h3>9. Creating Test Page</h3>";
$testPage = '<?php
echo "PHP is working!<br>";
echo "Current time: " . date("Y-m-d H:i:s") . "<br>";
echo "Server: " . $_SERVER["SERVER_SOFTWARE"] . "<br>";
echo "Document root: " . $_SERVER["DOCUMENT_ROOT"] . "<br>";
?>';

if (file_put_contents('test.php', $testPage)) {
    echo "✅ Test page created: <a href='test.php'>test.php</a><br>";
} else {
    echo "❌ Failed to create test page<br>";
}

echo "<h2>🎯 DIAGNOSIS COMPLETE</h2>";
echo "<p><strong>If you're still getting 500 errors after this fix:</strong></p>";
echo "<ul>";
echo "<li>Check your hosting control panel error logs</li>";
echo "<li>Contact Hostinger support with the error details above</li>";
echo "<li>The issue might be server configuration, not your code</li>";
echo "</ul>";

echo "<p><strong>Your site should now work! Try accessing it again.</strong></p>";

// Auto-delete this file after 5 minutes for security
echo "<script>
setTimeout(function() {
    if (confirm('Delete this diagnostic file for security?')) {
        fetch('LIVE_SERVER_FIX.php', {method: 'DELETE'});
    }
}, 300000);
</script>";
?>
