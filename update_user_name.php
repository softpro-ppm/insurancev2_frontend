<?php

// Simple script to update user name from 'Test' to 'Admin'
// This can be run from the command line or via web browser

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

try {
    echo "Updating user names...\n";
    
    // Find all users with name 'Test'
    $testUsers = User::where('name', 'Test')->get();
    
    if ($testUsers->count() > 0) {
        echo "Found " . $testUsers->count() . " user(s) with name 'Test'\n";
        
        // Update them to 'Admin'
        $updated = User::where('name', 'Test')->update(['name' => 'Admin']);
        echo "Updated $updated user(s) to 'Admin'\n";
    } else {
        echo "No users found with name 'Test'\n";
    }
    
    // Show current users
    echo "\nCurrent users:\n";
    $users = User::all(['id', 'name', 'email']);
    foreach ($users as $user) {
        echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
    }
    
    echo "\n✅ User name update completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure you have proper database configuration.\n";
}
?>
