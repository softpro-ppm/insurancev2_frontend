<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Followup;
use App\Models\Policy;
use App\Models\Agent;
use Carbon\Carbon;

class FollowupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing followups
        Followup::truncate();

        // Get some policies and agents for reference
        $policies = Policy::inRandomOrder()->limit(50)->get();
        $agents = Agent::all();
        $agentNames = $agents->pluck('name')->toArray();
        $agentNames[] = 'Self';

        $followupTypes = ['Renewal', 'New Policy', 'Claim', 'Document Collection', 'Payment Followup', 'Policy Update'];
        $statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled', 'Rescheduled'];
        $policyTypes = ['Motor', 'Health', 'Life', 'Home', 'Travel', 'Business'];

        $followups = [];

        // Generate followups for policies that are expiring soon
        $expiringPolicies = Policy::where('end_date', '>=', now())
                                 ->where('end_date', '<=', now()->addDays(30))
                                 ->limit(20)
                                 ->get();

        foreach ($expiringPolicies as $policy) {
            $followups[] = [
                'customer_name' => $policy->customer_name,
                'phone' => $policy->phone,
                'email' => $policy->email,
                'policy_type' => $policy->policy_type,
                'followup_type' => 'Renewal',
                'followup_date' => now()->addDays(rand(1, 15))->toDateString(),
                'followup_time' => $this->generateTime(),
                'status' => $statuses[array_rand($statuses)],
                'agent_name' => $policy->agent_name,
                'notes' => $this->generateRenewalNotes($policy)
            ];
        }

        // Generate followups for expired policies
        $expiredPolicies = Policy::where('status', 'Expired')->limit(15)->get();
        foreach ($expiredPolicies as $policy) {
            $followups[] = [
                'customer_name' => $policy->customer_name,
                'phone' => $policy->phone,
                'email' => $policy->email,
                'policy_type' => $policy->policy_type,
                'followup_type' => 'Renewal',
                'followup_date' => now()->addDays(rand(1, 7))->toDateString(),
                'followup_time' => $this->generateTime(),
                'status' => 'Pending',
                'agent_name' => $policy->agent_name,
                'notes' => $this->generateExpiredPolicyNotes($policy)
            ];
        }

        // Generate followups for new policy inquiries
        for ($i = 0; $i < 20; $i++) {
            $followups[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'followup_type' => 'New Policy',
                'followup_date' => now()->addDays(rand(1, 30))->toDateString(),
                'followup_time' => $this->generateTime(),
                'status' => $statuses[array_rand($statuses)],
                'agent_name' => $agentNames[array_rand($agentNames)],
                'notes' => $this->generateNewPolicyNotes()
            ];
        }

        // Generate followups for document collection
        for ($i = 0; $i < 15; $i++) {
            $followups[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'followup_type' => 'Document Collection',
                'followup_date' => now()->addDays(rand(1, 14))->toDateString(),
                'followup_time' => $this->generateTime(),
                'status' => $statuses[array_rand($statuses)],
                'agent_name' => $agentNames[array_rand($agentNames)],
                'notes' => $this->generateDocumentCollectionNotes()
            ];
        }

        // Generate followups for payment followups
        for ($i = 0; $i < 10; $i++) {
            $followups[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'followup_type' => 'Payment Followup',
                'followup_date' => now()->addDays(rand(1, 10))->toDateString(),
                'followup_time' => $this->generateTime(),
                'status' => $statuses[array_rand($statuses)],
                'agent_name' => $agentNames[array_rand($agentNames)],
                'notes' => $this->generatePaymentFollowupNotes()
            ];
        }

        // Generate followups for claims
        for ($i = 0; $i < 8; $i++) {
            $followups[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'followup_type' => 'Claim',
                'followup_date' => now()->addDays(rand(1, 5))->toDateString(),
                'followup_time' => $this->generateTime(),
                'status' => $statuses[array_rand($statuses)],
                'agent_name' => $agentNames[array_rand($agentNames)],
                'notes' => $this->generateClaimNotes()
            ];
        }

        // Generate followups for policy updates
        for ($i = 0; $i < 12; $i++) {
            $followups[] = [
                'customer_name' => $this->generateCustomerName(),
                'phone' => '+91' . rand(7000000000, 9999999999),
                'email' => $this->generateEmail(),
                'policy_type' => $policyTypes[array_rand($policyTypes)],
                'followup_type' => 'Policy Update',
                'followup_date' => now()->addDays(rand(1, 20))->toDateString(),
                'followup_time' => $this->generateTime(),
                'status' => $statuses[array_rand($statuses)],
                'agent_name' => $agentNames[array_rand($agentNames)],
                'notes' => $this->generatePolicyUpdateNotes()
            ];
        }

        foreach ($followups as $followup) {
            Followup::create($followup);
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

    private function generateTime()
    {
        $hours = rand(9, 18);
        $minutes = rand(0, 59);
        return sprintf('%02d:%02d:00', $hours, $minutes);
    }

    private function generateRenewalNotes($policy)
    {
        $notes = [
            "Policy #{$policy->id} expiring soon. Contact customer for renewal discussion.",
            "Follow up on renewal options for {$policy->policy_type} policy. Customer interested in upgrading coverage.",
            "Renewal reminder sent. Awaiting customer response for policy renewal.",
            "Discuss premium changes and new features for policy renewal.",
            "Customer requested renewal quote. Need to provide updated premium details.",
            "Renewal documents pending. Follow up for required paperwork.",
            "Policy renewal due. Customer has questions about coverage changes.",
            "Renewal process initiated. Waiting for customer confirmation.",
            "Premium payment reminder for policy renewal.",
            "Discuss additional riders available for renewal."
        ];
        return $notes[array_rand($notes)];
    }

    private function generateExpiredPolicyNotes($policy)
    {
        $notes = [
            "Policy #{$policy->id} expired. Urgent follow up required for renewal.",
            "Customer unaware of policy expiry. Need to explain implications and renewal process.",
            "Expired policy follow up. Customer interested in reinstatement options.",
            "Policy lapsed. Discussing renewal with grace period benefits.",
            "Expired policy renewal. Customer requesting special consideration.",
            "Follow up on expired policy. Customer wants to continue coverage.",
            "Policy expired. Need to explain renewal process and any penalties.",
            "Expired policy discussion. Customer considering switching companies.",
            "Policy lapsed follow up. Discussing reinstatement requirements.",
            "Expired policy renewal. Customer has new requirements."
        ];
        return $notes[array_rand($notes)];
    }

    private function generateNewPolicyNotes()
    {
        $notes = [
            "Customer inquired about new policy options. Need to discuss requirements.",
            "New policy inquiry. Customer comparing different insurance companies.",
            "Policy quote requested. Customer wants comprehensive coverage details.",
            "New customer interested in insurance. Need to understand their needs.",
            "Policy comparison requested. Customer evaluating multiple options.",
            "New policy discussion. Customer has specific coverage requirements.",
            "Insurance needs assessment. Customer wants to explore different products.",
            "New policy inquiry. Customer referred by existing client.",
            "Policy consultation scheduled. Customer wants detailed explanation.",
            "New business opportunity. Customer interested in multiple policies."
        ];
        return $notes[array_rand($notes)];
    }

    private function generateDocumentCollectionNotes()
    {
        $notes = [
            "Documents pending for policy processing. Follow up for required paperwork.",
            "Customer needs to submit additional documents. Reminder sent.",
            "Document verification in progress. Awaiting customer response.",
            "Policy documents incomplete. Need to collect missing information.",
            "Document collection follow up. Customer has questions about requirements.",
            "Policy processing delayed due to missing documents. Urgent follow up needed.",
            "Documents under review. Customer requested clarification on requirements.",
            "Policy approval pending document submission. Following up with customer.",
            "Document collection in progress. Customer working on gathering paperwork.",
            "Policy documents being verified. Need additional information from customer."
        ];
        return $notes[array_rand($notes)];
    }

    private function generatePaymentFollowupNotes()
    {
        $notes = [
            "Premium payment overdue. Following up for payment arrangement.",
            "Payment reminder sent. Customer requested payment plan options.",
            "Premium payment follow up. Customer experiencing financial difficulties.",
            "Payment overdue. Discussing installment options with customer.",
            "Premium payment reminder. Customer wants to discuss payment methods.",
            "Payment follow up required. Customer has payment-related questions.",
            "Premium payment pending. Customer requested payment extension.",
            "Payment overdue follow up. Discussing payment alternatives.",
            "Premium payment reminder. Customer interested in auto-debit setup.",
            "Payment follow up. Customer wants to discuss premium adjustments."
        ];
        return $notes[array_rand($notes)];
    }

    private function generateClaimNotes()
    {
        $notes = [
            "Claim filed by customer. Following up on claim status and documentation.",
            "Claim processing in progress. Need additional information from customer.",
            "Claim follow up required. Customer has questions about process.",
            "Claim under investigation. Regular updates needed for customer.",
            "Claim documents submitted. Awaiting insurance company response.",
            "Claim status update. Customer wants to discuss settlement options.",
            "Claim processing follow up. Need to coordinate with customer and company.",
            "Claim investigation ongoing. Customer requires regular communication.",
            "Claim settlement discussion. Customer has questions about offer.",
            "Claim follow up. Customer wants to understand next steps."
        ];
        return $notes[array_rand($notes)];
    }

    private function generatePolicyUpdateNotes()
    {
        $notes = [
            "Policy update requested by customer. Need to discuss changes and implications.",
            "Policy modification in progress. Customer wants to add additional coverage.",
            "Policy update follow up. Customer has questions about changes.",
            "Policy amendment requested. Need to explain process and requirements.",
            "Policy update discussion. Customer wants to modify coverage limits.",
            "Policy change follow up. Customer interested in upgrading coverage.",
            "Policy modification process. Customer needs guidance on requirements.",
            "Policy update consultation. Customer wants to discuss options.",
            "Policy change follow up. Customer has specific modification requests.",
            "Policy update discussion. Customer wants to understand impact on premium."
        ];
        return $notes[array_rand($notes)];
    }
}
