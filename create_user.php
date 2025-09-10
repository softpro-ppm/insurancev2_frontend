<?php
// create_user.php - Run this on your production server to create a user

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Check if user already exists
    $existingUser = User::where('email', 'admin@test.com')->first();
    
    if ($existingUser) {
        echo "User admin@test.com already exists!\n";
        echo "User ID: " . $existingUser->id . "\n";
        echo "User Name: " . $existingUser->name . "\n";
        echo "Email: " . $existingUser->email . "\n";
        echo "Created: " . $existingUser->created_at . "\n";
    } else {
        // Create new user
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        echo "User created successfully!\n";
        echo "User ID: " . $user->id . "\n";
        echo "User Name: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Password: password\n";
    }
    
    // Also create a simple test user
    $testUser = User::where('email', 'test@test.com')->first();
    if (!$testUser) {
        User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('test123'),
            'email_verified_at' => now(),
        ]);
        echo "Test user created: test@test.com / test123\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Make sure you're running this from the Laravel project root directory.\n";
}
