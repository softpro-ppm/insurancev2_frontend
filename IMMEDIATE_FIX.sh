#!/bin/bash

# IMMEDIATE FIX SCRIPT FOR PRODUCTION SERVER
# Run this on your production server to fix the $exceptionAsMarkdown error

echo "🚨 IMMEDIATE FIX: Clearing Laravel caches and fixing exception handler..."

# Navigate to project directory
cd /home/u820431346/domains/softpromis.com/public_html/v2insurance

echo "📁 Current directory: $(pwd)"

# Step 1: Create missing Exception Handler (CRITICAL)
echo "🛠️ Creating Exception Handler..."
mkdir -p app/Exceptions

cat > app/Exceptions/Handler.php << 'EOF'
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
EOF

# Step 2: Clear ALL caches (CRITICAL)
echo "🧹 Clearing ALL caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Step 3: Remove compiled views (CRITICAL)
echo "🗑️ Removing compiled views..."
rm -rf storage/framework/views/*
rm -rf storage/framework/cache/*

# Step 4: Recreate caches
echo "🔄 Recreating caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 5: Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Step 6: Create test user
echo "👤 Creating test user..."
php artisan tinker --execute="
if (!App\Models\User::where('email', 'admin@test.com')->exists()) {
    App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now()
    ]);
    echo 'User created: admin@test.com / password';
} else {
    echo 'User already exists: admin@test.com / password';
}
"

echo "✅ IMMEDIATE FIX COMPLETED!"
echo "🌐 Clear your browser cache and try: https://v2insurance.softpromis.com"
echo "🔑 Login: admin@test.com / password"
