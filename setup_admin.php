<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Setting up admin user for Hostinger deployment...\n";

// Create admin user (this will work for both local and Hostinger)
$adminUser = User::firstOrCreate(
    ['email' => 'admin@insurance.com'],
    [
        'name' => 'Admin',
        'password' => bcrypt('admin123'),
        'email_verified_at' => now(),
    ]
);

echo "✅ Admin user ready!\n";
echo "Email: admin@insurance.com\n";
echo "Password: admin123\n";
echo "Name: Admin\n";
echo "ID: " . $adminUser->id . "\n";

echo "\n📋 Instructions for Hostinger:\n";
echo "1. Deploy the updated files to Hostinger\n";
echo "2. Run this script on Hostinger: php setup_admin.php\n";
echo "3. Or manually create the admin user in phpMyAdmin\n";
