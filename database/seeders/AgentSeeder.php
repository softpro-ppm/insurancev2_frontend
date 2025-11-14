<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Agent;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing agents
        Agent::truncate();

        $agents = [
            [
                'name' => 'John Smith',
                'phone' => '+919876543210',
                'email' => 'john.smith@insurance.com',
                'user_id' => 'AG001',
                'status' => 'Active',
                'policies_count' => 35,
                'performance' => 85.50,
                'address' => 'Mumbai, Maharashtra',
                'password' => 'password123'
            ],
            [
                'name' => 'Sarah Johnson',
                'phone' => '+919876543211',
                'email' => 'sarah.johnson@insurance.com',
                'user_id' => 'AG002',
                'status' => 'Active',
                'policies_count' => 42,
                'performance' => 92.25,
                'address' => 'Delhi, NCR',
                'password' => 'password123'
            ],
            [
                'name' => 'Michael Brown',
                'phone' => '+919876543212',
                'email' => 'michael.brown@insurance.com',
                'user_id' => 'AG003',
                'status' => 'Active',
                'policies_count' => 23,
                'performance' => 78.75,
                'address' => 'Bangalore, Karnataka',
                'password' => 'password123'
            ]
        ];

        foreach ($agents as $agent) {
            Agent::create($agent);
        }
    }
}
