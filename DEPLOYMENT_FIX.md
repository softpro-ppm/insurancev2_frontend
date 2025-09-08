# Laravel Encryption Error Fix

## Problem
Your Laravel application is showing this error:
```
RuntimeException: Unsupported cipher or incorrect key length. Supported ciphers are: aes-128-cbc, aes-256-cbc, aes-128-gcm, aes-256-gcm.
```

## Root Cause
The application is missing the `APP_KEY` environment variable on your production server (Hostinger).

## Solution

### Step 1: Create .env file on your server
You need to create a `.env` file in your production server at the root of your Laravel application with this content:

```env
APP_NAME="Insurance Management V2"
APP_ENV=production
APP_KEY=base64:ZcluVpE3zyA3myeyjGI7Il2ne22PwkITV0Y7mX+YmNI=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://v2.insurance.softpromis.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=/home/u569469677/domains/v2.insurance.softpromis.com/public_html/database/database.sqlite

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 2: Upload .env file to server
1. Log into your Hostinger control panel
2. Go to File Manager
3. Navigate to `/domains/v2.insurance.softpromis.com/public_html/`
4. Create a new file named `.env` (with the dot at the beginning)
5. Copy and paste the content above into this file
6. Save the file

### Step 3: Set correct file permissions
The `.env` file should have restricted permissions for security:
- File permissions: 644 or 600
- Make sure only your user can read it

### Step 4: Clear Laravel cache (if possible)
If you have SSH access, run these commands:
```bash
php artisan config:clear
php artisan cache:clear
```

If you don't have SSH access, you can create a temporary PHP script to clear cache:
```php
<?php
// clear-cache.php - Delete this file after use!
require_once 'bootstrap/app.php';
$app = require_once 'bootstrap/app.php';
Artisan::call('config:clear');
Artisan::call('cache:clear');
echo "Cache cleared successfully!";
?>
```

## Important Security Notes

1. **Never commit .env files to version control** - they contain sensitive information
2. **Use different APP_KEY for each environment** (development, staging, production)
3. **Set proper file permissions** on .env file (644 or 600)
4. **Keep APP_KEY secret** - if compromised, generate a new one

## Alternative: If you can't create .env file

If your hosting provider doesn't allow .env files, you can set the APP_KEY directly in `config/app.php`:

```php
'key' => 'base64:ZcluVpE3zyA3myeyjGI7Il2ne22PwkITV0Y7mX+YmNI=',
```

But this is **NOT recommended** for security reasons.

## Testing the Fix

After implementing the solution:
1. Visit your website: https://v2.insurance.softpromis.com
2. The encryption error should be resolved
3. Your Laravel application should load normally

## Generated APP_KEY
Your application key: `base64:ZcluVpE3zyA3myeyjGI7Il2ne22PwkITV0Y7mX+YmNI=`

**Keep this key secure and don't share it publicly!**
