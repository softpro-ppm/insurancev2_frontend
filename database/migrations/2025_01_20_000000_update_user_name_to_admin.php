<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any user with name 'Test' to 'Admin'
        User::where('name', 'Test')->update([
            'name' => 'Admin'
        ]);
        
        // Also ensure the default admin user exists with correct name
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert the changes
        User::where('name', 'Admin')->update([
            'name' => 'Test'
        ]);
    }
};
