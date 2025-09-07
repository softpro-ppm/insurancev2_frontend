<?php
// Simple migration script for Hostinger deployment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run migrations
echo "Running database migrations...\n";
$kernel->call('migrate', ['--force' => true]);
echo "Migrations completed!\n";

// Optional: Run seeders (uncomment if needed)
// echo "Running database seeders...\n";
// $kernel->call('db:seed', ['--force' => true]);
// echo "Seeders completed!\n";

echo "Database setup complete!\n";
