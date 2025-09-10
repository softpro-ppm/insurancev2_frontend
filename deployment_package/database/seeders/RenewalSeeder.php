<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Renewal;
use App\Models\Policy;
use Carbon\Carbon;

class RenewalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing renewals
        Renewal::truncate();

        // Get some policies for reference
        $policies = Policy::where('status', 'Active')->limit(30)->get();
        
        $statuses = ['Pending', 'Completed', 'Overdue', 'Scheduled'];
        $agentNames = ['Rajesh Kumar', 'Priya Sharma', 'Amit Patel', 'Self'];

        $renewals = [];

        foreach ($policies as $policy) {
            // Create renewal for policies expiring in the next 60 days
            if ($policy->end_date && $policy->end_date->diffInDays(now()) <= 60) {
                $renewals[] = [
                    'customer_name' => $policy->customer_name,
                    'phone' => $policy->phone,
                    'email' => $policy->email,
                    'policy_type' => $policy->policy_type,
                    'current_premium' => $policy->premium,
                    'renewal_premium' => $policy->premium * (1 + (rand(-5, 15) / 100)), // 5% decrease to 15% increase
                    'due_date' => $policy->end_date->subDays(rand(0, 30))->toDateString(),
                    'status' => $statuses[array_rand($statuses)],
                    'agent_name' => $policy->agent_name,
                    'notes' => $this->generateRenewalNotes($policy),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        // Create some additional renewals for variety
        for ($i = 0; $i < 20; $i++) {
            $renewals[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => ['Motor', 'Health', 'Life'][array_rand(['Motor', 'Health', 'Life'])],
                'current_premium' => rand(5000, 50000),
                'renewal_premium' => rand(5500, 55000),
                'due_date' => now()->addDays(rand(1, 60))->toDateString(),
                'status' => $statuses[array_rand($statuses)],
                'agent_name' => $agentNames[array_rand($agentNames)],
                'notes' => $this->generateGenericNotes(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insert all renewals
        Renewal::insert($renewals);
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

    private function generateRenewalNotes($policy)
    {
        $notes = [
            "Policy #{$policy->id} renewal due. Contact customer for premium discussion.",
            "Renewal reminder sent. Customer interested in upgrading coverage.",
            "Premium increase notification sent. Awaiting customer response.",
            "Discuss new features and coverage options for renewal.",
            "Customer requested renewal quote. Premium adjustment required.",
            "Renewal documents pending. Follow up for required paperwork.",
            "Policy renewal due. Customer has questions about coverage changes.",
            "Renewal process initiated. Waiting for customer confirmation.",
            "Premium payment reminder for policy renewal.",
            "Discuss additional riders available for renewal."
        ];
        return $notes[array_rand($notes)];
    }

    private function generateGenericNotes()
    {
        $notes = [
            "Renewal reminder sent to customer.",
            "Customer requested renewal quote.",
            "Premium adjustment discussion needed.",
            "Renewal documents pending collection.",
            "Follow up on renewal decision.",
            "Customer interested in policy upgrade.",
            "Renewal process in progress.",
            "Awaiting customer response on renewal.",
            "Premium payment reminder sent.",
            "Renewal consultation scheduled."
        ];
        return $notes[array_rand($notes)];
    }
}
