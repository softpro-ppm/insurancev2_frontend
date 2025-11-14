<?php
/**
 * Export Production Policies to JSON
 * Upload this to Hostinger and run it to export all policies
 * URL: https://v2insurance.softpromis.com/export_policies_production.php
 */

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="policies_export_' . date('Y-m-d_H-i-s') . '.json"');

try {
    // Export agents
    $agents = \DB::table('agents')->get()->map(function($agent) {
        return (array) $agent;
    });

    // Export policies with related data (only versions - documents/renewals don't exist as relationships)
    $policies = \App\Models\Policy::with(['versions'])
        ->get()
        ->map(function($policy) {
            return [
                // Policy basic info
                'id' => $policy->id,
                'customer_name' => $policy->customer_name,
                'email' => $policy->email,
                'phone' => $policy->phone,
                
                // Policy type specific fields
                'policy_type' => $policy->policy_type,
                'business_type' => $policy->business_type,
                'agent_name' => $policy->agent_name,
                
                // Motor specific
                'vehicle_number' => $policy->vehicle_number,
                'vehicle_type' => $policy->vehicle_type,
                
                // Health/Life specific
                'customer_age' => $policy->customer_age,
                'customer_gender' => $policy->customer_gender,
                'sum_insured' => $policy->sum_insured,
                'sum_assured' => $policy->sum_assured,
                'policy_term' => $policy->policy_term,
                'premium_frequency' => $policy->premium_frequency,
                
                // Policy details
                'policy_number' => $policy->policy_number,
                'company_name' => $policy->company_name,
                'insurance_type' => $policy->insurance_type,
                'start_date' => $policy->start_date,
                'end_date' => $policy->end_date,
                'premium' => $policy->premium,
                'payout' => $policy->payout,
                'customer_paid_amount' => $policy->customer_paid_amount,
                'revenue' => $policy->revenue,
                'status' => $policy->status,
                
                // Document paths (stored as fields, not relationships)
                'policy_copy_path' => $policy->policy_copy_path,
                'rc_copy_path' => $policy->rc_copy_path,
                'aadhar_copy_path' => $policy->aadhar_copy_path,
                'pan_copy_path' => $policy->pan_copy_path,
                
                // Timestamps
                'created_at' => $policy->created_at,
                'updated_at' => $policy->updated_at,
                
                // Related data - Export actual versions, not just count
                'versions' => $policy->versions->map(function($version) {
                    return [
                        'id' => $version->id,
                        'policy_id' => $version->policy_id,
                        'version' => $version->version,
                        'start_date' => $version->start_date,
                        'end_date' => $version->end_date,
                        'premium' => $version->premium,
                        'changes' => $version->changes,
                        'created_at' => $version->created_at,
                        'updated_at' => $version->updated_at,
                    ];
                })->toArray(),
            ];
        });

    $export = [
        'exported_at' => now()->toDateTimeString(),
        'total_policies' => $policies->count(),
        'total_agents' => $agents->count(),
        'server' => 'production',
        'agents' => $agents->toArray(),
        'policies' => $policies->toArray()
    ];

    echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

