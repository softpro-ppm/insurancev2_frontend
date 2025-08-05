<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Policy;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'policy_number' => 'POL001',
                'customer_name' => 'Rajesh Kumar',
                'phone' => '+919876543210',
                'email' => 'rajesh@example.com',
                'policy_type' => 'Motor',
                'vehicle_number' => 'MH12AB1234',
                'vehicle_type' => 'Car',
                'company_name' => 'ICICI Lombard General Insurance Co. Ltd.',
                'insurance_type' => 'Comprehensive',
                'start_date' => '2024-01-15',
                'end_date' => '2025-01-14',
                'premium' => 8500.00,
                'payout' => 0.00,
                'customer_paid_amount' => 8500.00,
                'revenue' => 1200.00,
                'status' => 'Active',
                'business_type' => 'Agent1',
                'agent_name' => 'Priya Sharma'
            ],
            [
                'policy_number' => 'POL002',
                'customer_name' => 'Amit Patel',
                'phone' => '+919876543211',
                'email' => 'amit@example.com',
                'policy_type' => 'Health',
                'vehicle_number' => null,
                'vehicle_type' => null,
                'company_name' => 'Bajaj Allianz General Insurance Co. Ltd.',
                'insurance_type' => 'Family Floater',
                'start_date' => '2024-02-01',
                'end_date' => '2025-01-31',
                'premium' => 12000.00,
                'payout' => 0.00,
                'customer_paid_amount' => 12000.00,
                'revenue' => 1800.00,
                'status' => 'Active',
                'business_type' => 'Self',
                'agent_name' => 'Self'
            ],
            [
                'policy_number' => 'POL003',
                'customer_name' => 'Sneha Singh',
                'phone' => '+919876543212',
                'email' => 'sneha@example.com',
                'policy_type' => 'Life',
                'vehicle_number' => null,
                'vehicle_type' => null,
                'company_name' => 'LIC of India',
                'insurance_type' => 'Term Insurance',
                'start_date' => '2024-03-01',
                'end_date' => '2034-02-28',
                'premium' => 15000.00,
                'payout' => 0.00,
                'customer_paid_amount' => 15000.00,
                'revenue' => 2250.00,
                'status' => 'Active',
                'business_type' => 'Agent2',
                'agent_name' => 'Vikram Malhotra'
            ],
            [
                'policy_number' => 'POL004',
                'customer_name' => 'Vikram Malhotra',
                'phone' => '+919876543213',
                'email' => 'vikram@example.com',
                'policy_type' => 'Motor',
                'vehicle_number' => 'DL01CD5678',
                'vehicle_type' => 'Bike',
                'company_name' => 'HDFC ERGO General Insurance Co. Ltd.',
                'insurance_type' => 'Third Party',
                'start_date' => '2024-01-20',
                'end_date' => '2025-01-19',
                'premium' => 2500.00,
                'payout' => 0.00,
                'customer_paid_amount' => 2500.00,
                'revenue' => 375.00,
                'status' => 'Active',
                'business_type' => 'Self',
                'agent_name' => 'Self'
            ],
            [
                'policy_number' => 'POL005',
                'customer_name' => 'Neha Gupta',
                'phone' => '+919876543214',
                'email' => 'neha@example.com',
                'policy_type' => 'Health',
                'vehicle_number' => null,
                'vehicle_type' => null,
                'company_name' => 'Tata AIG General Insurance Co. Ltd.',
                'insurance_type' => 'Individual',
                'start_date' => '2024-02-10',
                'end_date' => '2025-02-09',
                'premium' => 8000.00,
                'payout' => 0.00,
                'customer_paid_amount' => 8000.00,
                'revenue' => 1200.00,
                'status' => 'Active',
                'business_type' => 'Agent1',
                'agent_name' => 'Priya Sharma'
            ]
        ];

        foreach ($policies as $policy) {
            Policy::create($policy);
        }
    }
}
