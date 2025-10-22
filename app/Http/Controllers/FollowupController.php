<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Followup;
use App\Mail\PolicyExpiringSoon;
use App\Mail\PolicyExpiredUrgent;
use App\Mail\RenewalReminder;
use App\Mail\ThankYouRenewal;

class FollowupController extends Controller
{
    /**
     * Display a listing of followups
     */
    public function index()
    {
        $followups = Followup::orderByDesc('id')->get()->map(function ($followup) {
            return [
                'id' => $followup->id,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                // Frontend expects these names
                'assignedTo' => $followup->agent_name,
                'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
                // Use stored followup_date as the scheduled next follow-up date
                'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
                'recentNote' => Str::limit((string) $followup->notes, 140),
                'notes' => $followup->notes,
                'createdAt' => optional($followup->created_at)->format('Y-m-d')
            ];
        });
        
        return response()->json(['followups' => $followups]);
    }

    /**
     * Get CRM dashboard data including expiring policies and followup stats
     */
    public function getCrmDashboard()
    {
        // First, let's check if we have any policies at all
        $totalPolicies = \App\Models\Policy::count();
        \Log::info('Total policies in database: ' . $totalPolicies);
        
        // Only create sample data if we're in development or if explicitly requested
        // Don't auto-create on production unless specifically needed
        
        // Get policies expiring in next 30 days
        $expiringPolicies = \App\Models\Policy::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->where('status', 'Active')
            ->orderBy('end_date')
            ->get()
            ->map(function ($policy) {
                $daysUntilExpiry = now()->diffInDays($policy->end_date, false);
                return [
                    'id' => $policy->id,
                    'customerName' => $policy->customer_name,
                    'phone' => $policy->phone,
                    'email' => $policy->email,
                    'policyType' => $policy->policy_type,
                    'companyName' => $policy->company_name,
                    'endDate' => $policy->end_date->format('Y-m-d'),
                    'daysUntilExpiry' => $daysUntilExpiry,
                    'premium' => $policy->premium,
                    'status' => $daysUntilExpiry <= 7 ? 'Urgent' : ($daysUntilExpiry <= 15 ? 'Warning' : 'Normal')
                ];
            });

        \Log::info('Expiring policies count: ' . $expiringPolicies->count());

        // Get followup statistics
        $stats = [
            'totalFollowups' => Followup::count(),
            'pendingFollowups' => Followup::where('status', 'Pending')->count(),
            'completedToday' => Followup::where('status', 'Completed')
                ->whereDate('updated_at', today())->count(),
            'overdueFollowups' => Followup::where('status', 'Pending')
                ->where('followup_date', '<', today())->count(),
            'expiringPolicies' => $expiringPolicies->count(),
            'urgentPolicies' => $expiringPolicies->where('status', 'Urgent')->count()
        ];

        // Get recent followups
        $recentFollowups = Followup::orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(function ($followup) {
                return [
                    'id' => $followup->id,
                    'customerName' => $followup->customer_name,
                    'phone' => $followup->phone,
                    'status' => $followup->status,
                    'lastContact' => $followup->updated_at->format('M d, Y'),
                    'nextFollowup' => $followup->followup_date ? $followup->followup_date->format('M d, Y') : 'Not scheduled'
                ];
            });

        // Don't auto-create followups on production
        // Sample data should only be created when explicitly requested

        return response()->json([
            'stats' => $stats,
            'expiringPolicies' => $expiringPolicies,
            'recentFollowups' => $recentFollowups
        ]);
    }

    /**
     * Create followup from expiring policy
     */
    public function createFromPolicy(Request $request, $policyId)
    {
        $policy = \App\Models\Policy::findOrFail($policyId);
        
        $followup = Followup::create([
            'customer_name' => $policy->customer_name,
            'phone' => $policy->phone,
            'email' => $policy->email,
            'policy_type' => $policy->policy_type,
            'followup_type' => 'Renewal',
            'followup_date' => now()->addDays(1)->toDateString(),
            'followup_time' => '09:00:00',
            'status' => 'Pending',
            'agent_name' => 'Self',
            'notes' => "Policy expiring on {$policy->end_date->format('M d, Y')}. Premium: ₹{$policy->premium}"
        ]);

        return response()->json([
            'message' => 'Followup created successfully!',
            'followup' => $followup
        ]);
    }

    /**
     * Send email to client based on policy status
     */
    public function sendEmailToClient(Request $request, $policyId)
    {
        $policy = \App\Models\Policy::findOrFail($policyId);
        $emailType = $request->input('emailType', 'reminder');
        
        $agentName = 'Insurance Agent'; // You can get this from auth or config
        $agentPhone = '+91-9876543210'; // You can get this from auth or config
        $agentEmail = 'agent@insurance.com'; // You can get this from auth or config
        
        $daysUntilExpiry = now()->diffInDays($policy->end_date, false);
        $daysSinceExpiry = abs($daysUntilExpiry);
        
        try {
            switch ($emailType) {
                case 'expiring':
                    Mail::to($policy->email)->send(new PolicyExpiringSoon($policy, $daysUntilExpiry, $agentName, $agentPhone, $agentEmail));
                    break;
                case 'expired':
                    Mail::to($policy->email)->send(new PolicyExpiredUrgent($policy, $daysSinceExpiry, $agentName, $agentPhone, $agentEmail));
                    break;
                case 'reminder':
                    Mail::to($policy->email)->send(new RenewalReminder($policy, $daysUntilExpiry, $agentName, $agentPhone, $agentEmail));
                    break;
                case 'thankyou':
                    $newEndDate = $policy->end_date->addYear()->format('M d, Y');
                    $renewalPremium = $policy->premium * 1.1; // 10% increase for renewal
                    Mail::to($policy->email)->send(new ThankYouRenewal($policy, $newEndDate, $renewalPremium, $agentName, $agentPhone, $agentEmail));
                    break;
                default:
                    return response()->json(['error' => 'Invalid email type'], 400);
            }
            
            return response()->json([
                'message' => 'Email sent successfully!',
                'emailType' => $emailType
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get follow-ups for a specific customer by phone
     */
    public function getCustomerFollowups($phone)
    {
        $followups = Followup::where('phone', $phone)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($followup) {
                return [
                    'id' => $followup->id,
                    'notes' => $followup->notes,
                    'status' => $followup->status,
                    'created_at' => $followup->created_at->format('M d, Y H:i')
                ];
            });

        return response()->json(['followups' => $followups]);
    }

    /**
     * Save simple follow-up from modal
     */
    public function saveSimpleFollowup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'policyId' => 'required|integer',
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|string|max:255',
            'notes' => 'required|string|max:2000',
            'status' => 'required|string|in:Will Come,Sold,Closed,Name Transfered,Not Answered,Wrong Number,Not Working',
            'nextFollowupDate' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::create([
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'policy_type' => 'General', // Default since we're not storing policy type in followup
            'followup_type' => 'Renewal',
            'followup_date' => $request->nextFollowupDate ?? now()->addDays(1)->toDateString(),
            'followup_time' => '09:00:00',
            'status' => $request->status,
            'agent_name' => 'Self',
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Follow-up saved successfully!',
            'followup' => $followup
        ]);
    }

    /**
     * Create sample expiring policies for testing
     */
    public function createSampleExpiringPolicies()
    {
        // Check if we already have sample data to avoid duplicates
        $existingSamplePolicies = \App\Models\Policy::where('customer_name', 'John Doe')
            ->orWhere('customer_name', 'Jane Smith')
            ->orWhere('customer_name', 'Mike Johnson')
            ->count();
            
        if ($existingSamplePolicies > 0) {
            return response()->json([
                'message' => 'Sample data already exists!',
                'count' => $existingSamplePolicies
            ]);
        }
        $samplePolicies = [
            [
                'customer_name' => 'John Doe',
                'phone' => '9876543210',
                'email' => 'john@example.com',
                'policy_type' => 'Motor',
                'company_name' => 'SBI General',
                'end_date' => now()->addDays(5)->format('Y-m-d'),
                'premium' => 15000,
                'status' => 'Active',
                'start_date' => now()->subDays(300)->format('Y-m-d'),
                'vehicle_number' => 'KA01AB1234',
                'vehicle_type' => 'Car',
                'insurance_type' => 'Comprehensive',
                'payout' => 0,
                'customer_paid_amount' => 15000,
                'revenue' => 1500,
                'business_type' => 'Direct',
                'agent_name' => 'Agent1'
            ],
            [
                'customer_name' => 'Jane Smith',
                'phone' => '9876543211',
                'email' => 'jane@example.com',
                'policy_type' => 'Health',
                'company_name' => 'Future Generali',
                'end_date' => now()->addDays(12)->format('Y-m-d'),
                'premium' => 25000,
                'status' => 'Active',
                'start_date' => now()->subDays(300)->format('Y-m-d'),
                'vehicle_number' => null,
                'vehicle_type' => null,
                'insurance_type' => 'Health',
                'payout' => 0,
                'customer_paid_amount' => 25000,
                'revenue' => 2500,
                'business_type' => 'Direct',
                'agent_name' => 'Agent2'
            ],
            [
                'customer_name' => 'Mike Johnson',
                'phone' => '9876543212',
                'email' => 'mike@example.com',
                'policy_type' => 'Motor',
                'company_name' => 'HDFC ERGO',
                'end_date' => now()->addDays(25)->format('Y-m-d'),
                'premium' => 18000,
                'status' => 'Active',
                'start_date' => now()->subDays(300)->format('Y-m-d'),
                'vehicle_number' => 'KA02CD5678',
                'vehicle_type' => 'Bike',
                'insurance_type' => 'Third Party',
                'payout' => 0,
                'customer_paid_amount' => 18000,
                'revenue' => 1800,
                'business_type' => 'Direct',
                'agent_name' => 'Agent3'
            ]
        ];

        $createdCount = 0;
        foreach ($samplePolicies as $policyData) {
            try {
                \App\Models\Policy::create($policyData);
                $createdCount++;
            } catch (\Exception $e) {
                \Log::error('Error creating policy: ' . $e->getMessage());
            }
        }

        \Log::info('Created ' . $createdCount . ' sample policies');

        return response()->json([
            'message' => 'Sample expiring policies created successfully!',
            'count' => $createdCount
        ]);
    }

    /**
     * Create sample follow-ups for testing
     */
    private function createSampleFollowups()
    {
        $sampleFollowups = [
            [
                'customer_name' => 'John Doe',
                'phone' => '9876543210',
                'email' => 'john@example.com',
                'policy_type' => 'Motor',
                'followup_type' => 'Renewal',
                'followup_date' => now()->addDays(2)->format('Y-m-d'),
                'followup_time' => '10:00:00',
                'status' => 'Pending',
                'agent_name' => 'Self',
                'notes' => 'Customer interested in renewal, will call back tomorrow'
            ],
            [
                'customer_name' => 'Jane Smith',
                'phone' => '9876543211',
                'email' => 'jane@example.com',
                'policy_type' => 'Health',
                'followup_type' => 'Renewal',
                'followup_date' => now()->addDays(5)->format('Y-m-d'),
                'followup_time' => '14:00:00',
                'status' => 'Will Come',
                'agent_name' => 'Self',
                'notes' => 'Customer confirmed they will visit office next week'
            ],
            [
                'customer_name' => 'Mike Johnson',
                'phone' => '9876543212',
                'email' => 'mike@example.com',
                'policy_type' => 'Motor',
                'followup_type' => 'Renewal',
                'followup_date' => now()->addDays(1)->format('Y-m-d'),
                'followup_time' => '09:00:00',
                'status' => 'Not Answered',
                'agent_name' => 'Self',
                'notes' => 'No response to calls, will try again tomorrow'
            ]
        ];

        foreach ($sampleFollowups as $followupData) {
            try {
                Followup::create($followupData);
            } catch (\Exception $e) {
                \Log::error('Error creating followup: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check data status for production safety
     */
    public function checkDataStatus()
    {
        $totalPolicies = \App\Models\Policy::count();
        $totalFollowups = Followup::count();
        $expiringPolicies = \App\Models\Policy::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->where('status', 'Active')
            ->count();

        return response()->json([
            'totalPolicies' => $totalPolicies,
            'totalFollowups' => $totalFollowups,
            'expiringPolicies' => $expiringPolicies,
            'hasData' => $totalPolicies > 0 || $totalFollowups > 0,
            'needsSampleData' => $totalPolicies === 0 && $totalFollowups === 0
        ]);
    }

    /**
     * Store a newly created followup
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Align with frontend payload
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'followupType' => 'required|string|max:100',
            'status' => 'required|string|in:Pending,In Progress,Completed,No Response,Not Interested,Cancelled',
            'assignedTo' => 'nullable|string|max:255',
            'nextFollowupDate' => 'nullable|date',
            'reminderTime' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:2000',
            // Optional fields not persisted directly
            'policyId' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::create([
            // DB requires policy_type; use a sensible default since UI doesn't send
            'policy_type' => $request->input('policyType', 'General'),
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            // Use nextFollowupDate as the scheduled date
            'followup_date' => $request->nextFollowupDate ?? now()->toDateString(),
            'followup_time' => $request->reminderTime ? ($request->reminderTime . (strlen($request->reminderTime) === 5 ? ':00' : '')) : '09:00:00',
            'followup_type' => $request->followupType,
            'status' => $request->status,
            'notes' => $request->notes,
            'agent_name' => $request->assignedTo ?? 'Self',
        ]);

        return response()->json([
            'message' => 'Followup created successfully!',
            'followup' => [
                'id' => $followup->id,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                'assignedTo' => $followup->agent_name,
                'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
                'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
                'recentNote' => Str::limit((string) $followup->notes, 140),
                'notes' => $followup->notes,
                'createdAt' => optional($followup->created_at)->format('Y-m-d')
            ]
        ], 201);
    }

    /**
     * Display the specified followup
     */
    public function show($id)
    {
        $followup = Followup::findOrFail($id);
        
        return response()->json(['followup' => [
            'id' => $followup->id,
            'customerName' => $followup->customer_name,
            'phone' => $followup->phone,
            'email' => $followup->email,
            'followupType' => $followup->followup_type,
            'status' => $followup->status,
            'assignedTo' => $followup->agent_name,
            'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
            'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
            'recentNote' => Str::limit((string) $followup->notes, 140),
            'notes' => $followup->notes,
            'createdAt' => optional($followup->created_at)->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified followup
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'followupType' => 'required|string|max:100',
            'status' => 'required|string|in:Pending,In Progress,Completed,No Response,Not Interested,Cancelled',
            'assignedTo' => 'nullable|string|max:255',
            'nextFollowupDate' => 'nullable|date',
            'reminderTime' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:2000',
            'policyId' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::findOrFail($id);
        $followup->update([
            // Keep existing policy_type or set default
            'policy_type' => $followup->policy_type ?? $request->input('policyType', 'General'),
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'followup_date' => $request->nextFollowupDate ?? $followup->followup_date,
            'followup_time' => $request->reminderTime ? ($request->reminderTime . (strlen($request->reminderTime) === 5 ? ':00' : '')) : $followup->followup_time,
            'followup_type' => $request->followupType,
            'status' => $request->status,
            'notes' => $request->notes,
            'agent_name' => $request->assignedTo ?? $followup->agent_name,
        ]);

        return response()->json([
            'message' => 'Followup updated successfully!',
            'followup' => [
                'id' => $followup->id,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                'assignedTo' => $followup->agent_name,
                'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
                'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
                'recentNote' => Str::limit((string) $followup->notes, 140),
                'notes' => $followup->notes,
                'createdAt' => optional($followup->created_at)->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Remove the specified followup
     */
    public function destroy($id)
    {
        $followup = Followup::findOrFail($id);
        $followup->delete();

        return response()->json([
            'message' => 'Followup deleted successfully!',
            'id' => $id
        ]);
    }
} 