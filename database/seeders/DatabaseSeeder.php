<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed our application data
        $this->call([
            AgentSeeder::class,
            PolicySeeder::class,
            RenewalSeeder::class,
            FollowupSeeder::class,
            NotificationSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
