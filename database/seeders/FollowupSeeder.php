<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Followup;

class FollowupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if table is empty
        if (Followup::count() > 0) {
            return;
        }

        $examples = [
            [
                'customer_name' => 'Rahul Sharma',
                'phone' => '9876543210',
                'email' => 'rahul@example.com',
                'policy_type' => 'General',
                'followup_type' => 'Renewal',
                'followup_date' => now()->addDays(2)->toDateString(),
                'followup_time' => '10:00:00',
                'status' => 'Pending',
                'agent_name' => 'Self',
                'notes' => 'Call customer about upcoming renewal.'
            ],
            [
                'customer_name' => 'Priya Verma',
                'phone' => '9988776655',
                'email' => 'priya@example.com',
                'policy_type' => 'General',
                'followup_type' => 'New Policy',
                'followup_date' => now()->addDays(5)->toDateString(),
                'followup_time' => '11:30:00',
                'status' => 'In Progress',
                'agent_name' => 'Self',
                'notes' => 'Shared plan details; follow up for documents.'
            ],
            [
                'customer_name' => 'Amit Patel',
                'phone' => '9123456780',
                'email' => 'amit@example.com',
                'policy_type' => 'General',
                'followup_type' => 'Claim',
                'followup_date' => now()->subDay()->toDateString(),
                'followup_time' => '15:00:00',
                'status' => 'Completed',
                'agent_name' => 'Self',
                'notes' => 'Claim processed and closed.'
            ]
        ];

        foreach ($examples as $data) {
            Followup::create($data);
        }
    }
}
