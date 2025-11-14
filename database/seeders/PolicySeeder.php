<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Policy;
use Carbon\Carbon;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing policies
        Policy::truncate();

        $currentMonth = Carbon::now();
        $nextMonth = Carbon::now()->addMonth();
        
        $policies = [];

        // Insurance companies for variety
        $companies = [
            'The New India',
            'United India',
            'National Insurance',
            'The Oriental',
            'ICICI Lombard',
            'HDFC ERGO',
            'Bajaj Allianz',
            'Tata AIG',
            'Reliance General',
            'SBI General',
            'IFFCO-Tokio',
            'Royal Sundaram',
            'Kotak Mahindra',
            'Chola MS',
            'Shriram General',
            'Universal Sompo',
            'Future Generali',
            'Magma HDI',
            'Raheja QBE',
            'Go Digit',
            'ACKO',
            'Zuno'
        ];

        // Policy types for variety
        $policyTypes = ['Motor', 'Health', 'Life'];
        $insuranceTypes = ['Comprehensive', 'Stand Alone OD', 'Third Party'];
        $agents = ['John Smith', 'Sarah Johnson', 'Michael Brown', 'Self'];

        // 1. Issued 10 policies current month
        for ($i = 0; $i < 10; $i++) {
            $startDate = $currentMonth->copy()->subDays(rand(1, 30));
            $endDate = $startDate->copy()->addYear();
            
            $policies[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'vehicle_number' => $this->generateVehicleNumber(),
                'vehicle_type' => $this->generateVehicleType(),
                'company_name' => $companies[array_rand($companies)],
                'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'premium' => rand(2000, 25000),
                'payout' => 0.00,
                'customer_paid_amount' => rand(2000, 25000),
                'revenue' => rand(300, 3750),
                'status' => 'Active',
                'business_type' => $this->generateBusinessType(),
                'agent_name' => $agents[array_rand($agents)]
            ];
        }

        // 2. Expired 5 policies current month
        for ($i = 0; $i < 5; $i++) {
            $startDate = $currentMonth->copy()->subYear()->subDays(rand(1, 30));
            $endDate = $currentMonth->copy()->subDays(rand(1, 30));
            
            $policies[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'vehicle_number' => $this->generateVehicleNumber(),
                'vehicle_type' => $this->generateVehicleType(),
                'company_name' => $companies[array_rand($companies)],
                'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'premium' => rand(2000, 25000),
                'payout' => 0.00,
                'customer_paid_amount' => rand(2000, 25000),
                'revenue' => rand(300, 3750),
                'status' => 'Expired',
                'business_type' => $this->generateBusinessType(),
                'agent_name' => $agents[array_rand($agents)]
            ];
        }

        // 3. Expiring 12 policies current month
        for ($i = 0; $i < 12; $i++) {
            $startDate = $currentMonth->copy()->subYear()->subDays(rand(1, 30));
            $endDate = $currentMonth->copy()->addDays(rand(1, 30));
            
            $policies[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'vehicle_number' => $this->generateVehicleNumber(),
                'vehicle_type' => $this->generateVehicleType(),
                'company_name' => $companies[array_rand($companies)],
                'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'premium' => rand(2000, 25000),
                'payout' => 0.00,
                'customer_paid_amount' => rand(2000, 25000),
                'revenue' => rand(300, 3750),
                'status' => 'Active',
                'business_type' => $this->generateBusinessType(),
                'agent_name' => $agents[array_rand($agents)]
            ];
        }

        // 4. Expiring 7 policies in September 2025 (next month)
        for ($i = 0; $i < 7; $i++) {
            $startDate = $nextMonth->copy()->subYear()->subDays(rand(1, 30));
            $endDate = $nextMonth->copy()->addDays(rand(1, 30));
            
            $policies[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'vehicle_number' => $this->generateVehicleNumber(),
                'vehicle_type' => $this->generateVehicleType(),
                'company_name' => $companies[array_rand($companies)],
                'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'premium' => rand(2000, 25000),
                'payout' => 0.00,
                'customer_paid_amount' => rand(2000, 25000),
                'revenue' => rand(300, 3750),
                'status' => 'Active',
                'business_type' => $this->generateBusinessType(),
                'agent_name' => $agents[array_rand($agents)]
            ];
        }

        // 5. Expired 5 policies before July 2025
        for ($i = 0; $i < 5; $i++) {
            $startDate = Carbon::create(2024, 1, 1)->addDays(rand(1, 365));
            $endDate = Carbon::create(2025, 6, 30)->subDays(rand(1, 180));
            
            $policies[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'vehicle_number' => $this->generateVehicleNumber(),
                'vehicle_type' => $this->generateVehicleType(),
                'company_name' => $companies[array_rand($companies)],
                'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'premium' => rand(2000, 25000),
                'payout' => 0.00,
                'customer_paid_amount' => rand(2000, 25000),
                'revenue' => rand(300, 3750),
                'status' => 'Expired',
                'business_type' => $this->generateBusinessType(),
                'agent_name' => $agents[array_rand($agents)]
            ];
        }

        // 6. Expired 5 policies before June 2025
        for ($i = 0; $i < 5; $i++) {
            $startDate = Carbon::create(2024, 1, 1)->addDays(rand(1, 365));
            $endDate = Carbon::create(2025, 5, 31)->subDays(rand(1, 150));
            
            $policies[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'vehicle_number' => $this->generateVehicleNumber(),
                'vehicle_type' => $this->generateVehicleType(),
                'company_name' => $companies[array_rand($companies)],
                'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'premium' => rand(2000, 25000),
                'payout' => 0.00,
                'customer_paid_amount' => rand(2000, 25000),
                'revenue' => rand(300, 3750),
                'status' => 'Expired',
                'business_type' => $this->generateBusinessType(),
                'agent_name' => $agents[array_rand($agents)]
            ];
        }

        // 7. Remaining policies (56 policies) - mix of active and expired
        for ($i = 0; $i < 56; $i++) {
            $startDate = Carbon::create(2023, 1, 1)->addDays(rand(1, 730));
            $endDate = $startDate->copy()->addYear();
            
            // Randomly make some expired
            if (rand(1, 3) === 1) {
                $endDate = $startDate->copy()->addMonths(rand(6, 18));
            }
            
            $policies[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'vehicle_number' => $this->generateVehicleNumber(),
                'vehicle_type' => $this->generateVehicleType(),
                'company_name' => $companies[array_rand($companies)],
                'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'premium' => rand(2000, 25000),
                'payout' => 0.00,
                'customer_paid_amount' => rand(2000, 25000),
                'revenue' => rand(300, 3750),
                'status' => $endDate->isPast() ? 'Expired' : 'Active',
                'business_type' => $this->generateBusinessType(),
                'agent_name' => $agents[array_rand($agents)]
            ];
        }

        foreach ($policies as $policy) {
            Policy::create($policy);
        }
    }

    private function generateCustomerName()
    {
        $firstNames = ['Rajesh', 'Priya', 'Amit', 'Sneha', 'Vikram', 'Neha', 'Rahul', 'Anjali', 'Deepak', 'Pooja', 'Sanjay', 'Meera', 'Arun', 'Kavita', 'Ramesh', 'Sunita', 'Mohan', 'Reena', 'Suresh', 'Anita'];
        $lastNames = ['Kumar', 'Sharma', 'Patel', 'Singh', 'Malhotra', 'Gupta', 'Verma', 'Joshi', 'Yadav', 'Chopra', 'Kapoor', 'Reddy', 'Nair', 'Iyer', 'Menon', 'Pillai', 'Nayar', 'Menon', 'Nambiar', 'Kurup'];
        
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function generateEmail()
    {
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'rediffmail.com'];
        $name = strtolower(str_replace(' ', '', $this->generateCustomerName()));
        return $name . '@' . $domains[array_rand($domains)];
    }

    private function generateVehicleNumber()
    {
        $states = ['MH', 'DL', 'KA', 'TN', 'AP', 'TG', 'KL', 'GJ', 'MP', 'UP'];
        $state = $states[array_rand($states)];
        $district = rand(1, 99);
        $letters = chr(rand(65, 90)) . chr(rand(65, 90));
        $numbers = rand(1000, 9999);
        
        return $state . $district . $letters . $numbers;
    }

    private function generateVehicleType()
    {
        $types = [
            'Auto (G)',
            'Auto',
            'Bus',
            'Car (Taxi)',
            'Car',
            'E-Auto',
            'E-Car',
            'HGV',
            'JCB',
            'LCV',
            'Others',
            'Tractor',
            'Trailer',
            '2-Wheeler',
            'Van/Jeep'
        ];
        return $types[array_rand($types)];
    }

    private function generateBusinessType()
    {
        // 70% chance of Agent, 30% chance of Self
        return rand(1, 10) <= 7 ? 'Agent' : 'Self';
    }
}
