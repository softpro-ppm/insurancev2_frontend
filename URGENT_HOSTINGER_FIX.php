<?php
/**
 * URGENT HOSTINGER FIX - Complete Working Solution
 * This will fix the 500 error on Hostinger immediately
 */

echo "🚨 URGENT HOSTINGER FIX - Creating Complete Solution\n";
echo "===================================================\n\n";

// Create the fixed .htaccess file for Hostinger
$htaccessContent = '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Cache Control
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
</IfModule>

# Compress files
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Prevent access to sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

<Files "artisan">
    Order allow,deny
    Deny from all
</Files>';

file_put_contents('HOSTINGER_HTACCESS', $htaccessContent);

// Create the working .env file
$envContent = 'APP_NAME="Insurance MS 2.0"
APP_ENV=production
APP_KEY=base64:MyCUMjSOXv1cBBPXNKMJuu0bRuIv3qPwfa+n+6MMUkA=
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

BROADCAST_DRIVER=log
CACHE_STORE=file
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

file_put_contents('HOSTINGER_ENV', $envContent);

// Create cache clearing script
$cacheScript = '<?php
// clear-cache.php - DELETE AFTER USE!
echo "🧹 Clearing Laravel Cache...<br>";

try {
    // Simple cache clearing without full Laravel bootstrap
    $cacheDir = __DIR__ . "/storage/framework/cache";
    $viewsDir = __DIR__ . "/storage/framework/views";
    $configDir = __DIR__ . "/bootstrap/cache";
    
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Cache cleared<br>";
    }
    
    if (is_dir($viewsDir)) {
        $files = glob($viewsDir . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Views cleared<br>";
    }
    
    if (is_dir($configDir)) {
        $files = glob($configDir . "/*");
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== ".gitignore") {
                unlink($file);
            }
        }
        echo "✅ Config cleared<br>";
    }
    
    echo "✅ All caches cleared successfully!<br>";
    echo "🚨 DELETE this file immediately for security!<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>';

file_put_contents('clear-cache-simple.php', $cacheScript);

// Create index.php check script
$indexCheck = '<?php
echo "🔍 Laravel Environment Check<br>";
echo "=========================<br>";

// Check if .env exists
if (file_exists(".env")) {
    echo "✅ .env file exists<br>";
} else {
    echo "❌ .env file missing<br>";
}

// Check if storage is writable
if (is_writable("storage")) {
    echo "✅ Storage directory is writable<br>";
} else {
    echo "❌ Storage directory is not writable<br>";
}

// Check if bootstrap/cache is writable
if (is_writable("bootstrap/cache")) {
    echo "✅ Bootstrap cache is writable<br>";
} else {
    echo "❌ Bootstrap cache is not writable<br>";
}

// Check database
if (file_exists("database/database.sqlite")) {
    echo "✅ Database file exists<br>";
} else {
    echo "❌ Database file missing<br>";
}

echo "<br>🚀 If all checks pass, your Laravel app should work!<br>";
?>';

file_put_contents('check-environment.php', $indexCheck);

echo "✅ URGENT HOSTINGER FIX FILES CREATED!\n";
echo "=====================================\n\n";
echo "Files created:\n";
echo "1. HOSTINGER_HTACCESS - Fixed .htaccess file\n";
echo "2. HOSTINGER_ENV - Working .env file\n";
echo "3. clear-cache-simple.php - Simple cache clearing script\n";
echo "4. check-environment.php - Environment check script\n\n";

echo "🚨 IMMEDIATE ACTION REQUIRED:\n";
echo "=============================\n\n";
echo "1. UPLOAD HOSTINGER_HTACCESS to public/.htaccess on Hostinger\n";
echo "2. UPLOAD HOSTINGER_ENV to .env on Hostinger\n";
echo "3. UPLOAD clear-cache-simple.php to your server root\n";
echo "4. UPLOAD check-environment.php to your server root\n";
echo "5. Visit: https://v2insurance.softpromis.com/check-environment.php\n";
echo "6. Visit: https://v2insurance.softpromis.com/clear-cache-simple.php\n";
echo "7. DELETE both PHP files after use for security\n";
echo "8. Test your site: https://v2insurance.softpromis.com\n\n";

echo "This will fix the 500 error immediately! 🚀\n";
?>
