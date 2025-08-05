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
        $agents = [
            [
                'name' => 'Rajesh Kumar',
                'phone' => '+919876543210',
                'email' => 'rajesh@example.com',
                'user_id' => 'AG001',
                'status' => 'Active',
                'policies_count' => 10,
                'performance' => 78.50,
                'address' => 'Mumbai, Maharashtra',
                'password' => 'password123'
            ],
            [
                'name' => 'Priya Sharma',
                'phone' => '+919876543211',
                'email' => 'priya@example.com',
                'user_id' => 'AG002',
                'status' => 'Active',
                'policies_count' => 8,
                'performance' => 81.25,
                'address' => 'Delhi, NCR',
                'password' => 'password123'
            ],
            [
                'name' => 'Amit Patel',
                'phone' => '+919876543212',
                'email' => 'amit@example.com',
                'user_id' => 'AG003',
                'status' => 'Active',
                'policies_count' => 12,
                'performance' => 80.00,
                'address' => 'Bangalore, Karnataka',
                'password' => 'password123'
            ],
            [
                'name' => 'Sneha Singh',
                'phone' => '+919876543213',
                'email' => 'sneha@example.com',
                'user_id' => 'AG004',
                'status' => 'Active',
                'policies_count' => 6,
                'performance' => 73.75,
                'address' => 'Chennai, Tamil Nadu',
                'password' => 'password123'
            ],
            [
                'name' => 'Vikram Malhotra',
                'phone' => '+919876543214',
                'email' => 'vikram@example.com',
                'user_id' => 'AG005',
                'status' => 'Active',
                'policies_count' => 14,
                'performance' => 95.00,
                'address' => 'Hyderabad, Telangana',
                'password' => 'password123'
            ],
            [
                'name' => 'Neha Gupta',
                'phone' => '+919876543215',
                'email' => 'neha@example.com',
                'user_id' => 'AG006',
                'status' => 'Active',
                'policies_count' => 0,
                'performance' => 77.50,
                'address' => 'Pune, Maharashtra',
                'password' => 'password123'
            ]
        ];

        foreach ($agents as $agent) {
            Agent::create($agent);
        }
    }
}
