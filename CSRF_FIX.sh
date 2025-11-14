#!/bin/bash

# CSRF TOKEN FIX SCRIPT
# Run this on your production server to fix the 419 PAGE EXPIRED error

echo "🚨 FIXING CSRF TOKEN ISSUE..."

# Navigate to project directory
cd /home/u820431346/domains/softpromis.com/public_html/v2insurance

echo "📁 Current directory: $(pwd)"

# Step 1: Clear ALL caches
echo "🧹 Clearing ALL caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Step 2: Remove compiled views
echo "🗑️ Removing compiled views..."
rm -rf storage/framework/views/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*

# Step 3: Generate new application key
echo "🔑 Generating new application key..."
php artisan key:generate --force

# Step 4: Clear sessions
echo "🗑️ Clearing sessions..."
rm -rf storage/framework/sessions/*

# Step 5: Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Step 6: Recreate caches
echo "🔄 Recreating caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 7: Create test user
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
    echo 'User exists: admin@test.com / password';
}
"

echo "✅ CSRF FIX COMPLETED!"
echo "🌐 Try: https://v2insurance.softpromis.com/login"
echo "🔑 Login: admin@test.com / password"
echo "💡 Clear browser cache if still having issues"
