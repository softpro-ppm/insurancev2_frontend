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
        
        // Debug: Let's see what's in the database
        $allPolicies = \App\Models\Policy::all();
        \Log::info('All policies in database: ' . $allPolicies->toJson());
        
        // Check if we have any policies with end_date
        $policiesWithEndDate = \App\Models\Policy::whereNotNull('end_date')->count();
        \Log::info('Policies with end_date: ' . $policiesWithEndDate);
        
        // Get policies expiring in next 30 days using SQL-based date conversion
        $today = now()->startOfDay();
        $endWindow = $today->copy()->addDays(30);
        
        // Use raw SQL to handle both date formats (dd-mm-yyyy and yyyy-mm-dd)
        $expiringPolicies = \App\Models\Policy::whereRaw(
            "STR_TO_DATE(end_date, '%d-%m-%Y') BETWEEN ? AND ? OR 
             STR_TO_DATE(end_date, '%Y-%m-%d') BETWEEN ? AND ?",
            [$today->toDateString(), $endWindow->toDateString(), $today->toDateString(), $endWindow->toDateString()]
        )->orderBy('end_date')
        ->get();
        
        // If still no results, try alternative approaches
        if ($expiringPolicies->count() === 0) {
            // Try with string comparison for dd-mm-yyyy format
            $todayStr = $today->format('d-m-Y');
            $endWindowStr = $endWindow->format('d-m-Y');
            
            $expiringPolicies = \App\Models\Policy::where('end_date', '>=', $todayStr)
                ->where('end_date', '<=', $endWindowStr)
                ->orderBy('end_date')
                ->get();
        }
        
        // If still no results, get all policies to debug
        if ($expiringPolicies->count() === 0) {
            $allPolicies = \App\Models\Policy::limit(5)->get();
            \Log::info('No expiring policies found. Sample policies:', $allPolicies->toArray());
        }
        
        $expiringPolicies = $expiringPolicies->map(function ($policy) {
            try {
                // Use smart date detection
                $endDate = $this->parseDate($policy->end_date);
                
                if (!$endDate) {
                    \Log::warning('Could not parse date for policy ID: ' . $policy->id . ', raw date: ' . $policy->end_date);
                    return null;
                }
                
                $daysUntilExpiry = now()->diffInDays($endDate, false);
                return [
                    'id' => $policy->id,
                    'customerName' => $policy->customer_name,
                    'phone' => $policy->phone,
                    'email' => $policy->email,
                    'policyType' => $policy->policy_type,
                    'companyName' => $policy->company_name,
                    'endDate' => $endDate->format('Y-m-d'),
                    'daysUntilExpiry' => $daysUntilExpiry,
                    'premium' => $policy->premium,
                    'status' => $daysUntilExpiry <= 7 ? 'Urgent' : ($daysUntilExpiry <= 15 ? 'Warning' : 'Normal')
                ];
            } catch (\Exception $e) {
                \Log::error('Error processing policy: ' . $e->getMessage());
                return null;
            }
        })->filter();

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
            'urgentPolicies' => $expiringPolicies->filter(function ($p) { return ($p['status'] ?? '') === 'Urgent'; })->count()
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
     * Smart date detection and parsing helper
     */
    private function parseDate($dateValue)
    {
        if (is_null($dateValue)) {
            return null;
        }
        
        if ($dateValue instanceof \Carbon\Carbon) {
            return $dateValue;
        }
        
        // Try dd-mm-yyyy format first
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateValue)) {
            try {
                return \Carbon\Carbon::createFromFormat('d-m-Y', $dateValue);
            } catch (\Exception $e) {
                \Log::error('Failed to parse dd-mm-yyyy date: ' . $dateValue . ' - ' . $e->getMessage());
            }
        }
        
        // Try yyyy-mm-dd format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
            try {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $dateValue);
            } catch (\Exception $e) {
                \Log::error('Failed to parse yyyy-mm-dd date: ' . $dateValue . ' - ' . $e->getMessage());
            }
        }
        
        // Try general parsing
        try {
            return \Carbon\Carbon::parse($dateValue);
        } catch (\Exception $e) {
            \Log::error('Failed to parse date: ' . $dateValue . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Debug database connection and data
     */
    public function debugDatabase()
    {
        try {
            // Test database connection
            $dbConnection = \DB::connection()->getPdo();
            $dbName = \DB::connection()->getDatabaseName();
            
            // Get table info
            $tables = \DB::select("SHOW TABLES");
            $policiesTableExists = \Schema::hasTable('policies');
            $followupsTableExists = \Schema::hasTable('followups');
            
            // Get actual data
            $totalPolicies = \App\Models\Policy::count();
            $totalFollowups = Followup::count();
            
            // Get sample policies
            $samplePolicies = \App\Models\Policy::limit(3)->get();
            $sampleFollowups = Followup::limit(3)->get();
            
            return response()->json([
                'database_connected' => true,
                'database_name' => $dbName,
                'tables' => $tables,
                'policies_table_exists' => $policiesTableExists,
                'followups_table_exists' => $followupsTableExists,
                'total_policies' => $totalPolicies,
                'total_followups' => $totalFollowups,
                'sample_policies' => $samplePolicies,
                'sample_followups' => $sampleFollowups,
                'current_time' => now()->toDateTimeString(),
                'timezone' => config('app.timezone')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'database_connected' => false
            ]);
        }
    }

    /**
     * Get Follow Up Dashboard - Policies grouped by expiry month
     */
    public function getFollowUpDashboard()
    {
        try {
            $today = now()->startOfDay();
            
            // Define date ranges - use copy() to avoid mutation
            $lastMonthStart = now()->copy()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->copy()->subMonth()->endOfMonth();
            
            $currentMonthStart = now()->copy()->startOfMonth();
            $currentMonthEnd = now()->copy()->endOfMonth();
            
            $nextMonthStart = now()->copy()->addMonth()->startOfMonth();
            $nextMonthEnd = now()->copy()->addMonth()->endOfMonth();
            
            \Log::info('Follow-up Dashboard Date Ranges:', [
                'lastMonth' => [$lastMonthStart->format('Y-m-d'), $lastMonthEnd->format('Y-m-d')],
                'currentMonth' => [$currentMonthStart->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')],
                'nextMonth' => [$nextMonthStart->format('Y-m-d'), $nextMonthEnd->format('Y-m-d')]
            ]);
            
            // Get ALL policies with end dates (not just Active, to match Renewals page behavior)
            // Eager-load versions so we can treat policies with versions as "already renewed"
            $allPolicies = \App\Models\Policy::with('versions')
                ->whereNotNull('end_date')
                ->get();
            
            \Log::info('Total active policies found: ' . $allPolicies->count());
            
            // Helper: consider a policy "renewed" if it has any versions (new version-based system)
            // Older multi-policy renewals without versions will be treated as pending for now,
            // which keeps this dashboard consistent with the new Renewals summary logic.
            $isRenewed = function($policy) {
                return $policy->relationLoaded('versions') && $policy->versions->count() > 0;
            };
            
            // Get last notes for policies
            $getLastNote = function($phone) {
                $lastFollowup = Followup::where('phone', $phone)
                    ->orderByDesc('created_at')
                    ->first();
                
                return $lastFollowup ? [
                    'note' => $lastFollowup->notes,
                    'status' => $lastFollowup->status,
                    'date' => $lastFollowup->created_at->format('d M Y')
                ] : null;
            };
            
            // Process and categorize policies
            $processPolicies = function($policies, $category) use ($isRenewed, $getLastNote, $today) {
                return $policies->map(function ($policy) use ($isRenewed, $getLastNote, $today, $category) {
                    // Skip if renewed (has versions in new system)
                    if ($isRenewed($policy)) {
                        return null;
                    }
                    
                    if (!$policy->end_date) return null;
                    
                    $daysUntilExpiry = $today->diffInDays($policy->end_date, false);
                    $lastNote = $getLastNote($policy->phone);

                    $rawType = strtolower($policy->policy_type ?? '');
                    $displayType = $policy->policy_type ?? '';
                    if ($rawType === 'motor') {
                        $displayType = $policy->vehicle_type ?: 'Motor';
                    } elseif ($rawType === 'health') {
                        $displayType = 'Health';
                    } elseif ($rawType === 'life') {
                        $displayType = 'Life';
                    }

                    return [
                        'id' => $policy->id,
                        'customerName' => $policy->customer_name,
                        'phone' => $policy->phone,
                        'email' => $policy->email ?? '',
                        'policyType' => $policy->policy_type,
                        'displayType' => $displayType,
                        'companyName' => $policy->company_name,
                        'endDate' => $policy->end_date->format('d M Y'),
                        'endDateRaw' => $policy->end_date->format('Y-m-d'),
                        'daysUntilExpiry' => $daysUntilExpiry,
                        'premium' => $policy->premium,
                        'category' => $category,
                        'lastNote' => $lastNote
                    ];
                })->filter()->values();
            };
            
            // Filter policies by date ranges using Carbon date comparison
            $lastMonthPolicies = $allPolicies->filter(function($policy) use ($lastMonthStart, $lastMonthEnd) {
                return $policy->end_date && 
                       $policy->end_date->between($lastMonthStart, $lastMonthEnd);
            });
            
            $currentMonthPolicies = $allPolicies->filter(function($policy) use ($currentMonthStart, $currentMonthEnd) {
                return $policy->end_date && 
                       $policy->end_date->between($currentMonthStart, $currentMonthEnd);
            });
            
            $nextMonthPolicies = $allPolicies->filter(function($policy) use ($nextMonthStart, $nextMonthEnd) {
                return $policy->end_date && 
                       $policy->end_date->between($nextMonthStart, $nextMonthEnd);
            });
            
            \Log::info('Filtered policies counts:', [
                'lastMonth' => $lastMonthPolicies->count(),
                'currentMonth' => $currentMonthPolicies->count(),
                'nextMonth' => $nextMonthPolicies->count()
            ]);
            
            // Process each category
            $lastMonth = $processPolicies($lastMonthPolicies, 'expired')->sortBy('daysUntilExpiry')->values();
            $currentMonth = $processPolicies($currentMonthPolicies, 'expiring')->sortBy('daysUntilExpiry')->values();
            $nextMonth = $processPolicies($nextMonthPolicies, 'upcoming')->sortBy('endDateRaw')->values();
            
            // Calculate expired vs expiring breakdown
            $lastMonthExpired = $lastMonth->filter(function($p) { return $p['daysUntilExpiry'] < 0; })->count();
            $lastMonthExpiring = $lastMonth->filter(function($p) { return $p['daysUntilExpiry'] >= 0; })->count();
            
            $currentMonthExpired = $currentMonth->filter(function($p) { return $p['daysUntilExpiry'] < 0; })->count();
            $currentMonthExpiring = $currentMonth->filter(function($p) { return $p['daysUntilExpiry'] >= 0; })->count();
            
            $nextMonthExpired = $nextMonth->filter(function($p) { return $p['daysUntilExpiry'] < 0; })->count();
            $nextMonthExpiring = $nextMonth->filter(function($p) { return $p['daysUntilExpiry'] >= 0; })->count();
            
            $totalExpired = $lastMonthExpired + $currentMonthExpired + $nextMonthExpired;
            $totalExpiring = $lastMonthExpiring + $currentMonthExpiring + $nextMonthExpiring;
            
            \Log::info('Processed policies counts:', [
                'lastMonth' => $lastMonth->count(),
                'currentMonth' => $currentMonth->count(),
                'nextMonth' => $nextMonth->count()
            ]);
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'lastMonth' => [
                        'expired' => $lastMonthExpired,
                        'expiring' => $lastMonthExpiring,
                        'total' => $lastMonth->count()
                    ],
                    'currentMonth' => [
                        'expired' => $currentMonthExpired,
                        'expiring' => $currentMonthExpiring,
                        'total' => $currentMonth->count()
                    ],
                    'nextMonth' => [
                        'expired' => $nextMonthExpired,
                        'expiring' => $nextMonthExpiring,
                        'total' => $nextMonth->count()
                    ],
                    'total' => [
                        'expired' => $totalExpired,
                        'expiring' => $totalExpiring,
                        'total' => $lastMonth->count() + $currentMonth->count() + $nextMonth->count()
                    ]
                ],
                'data' => [
                    'lastMonth' => $lastMonth,
                    'currentMonth' => $currentMonth,
                    'nextMonth' => $nextMonth
                ],
                'debug' => [
                    'totalPolicies' => $allPolicies->count(),
                    'dateRanges' => [
                        'lastMonth' => $lastMonthStart->format('Y-m-d') . ' to ' . $lastMonthEnd->format('Y-m-d'),
                        'currentMonth' => $currentMonthStart->format('Y-m-d') . ' to ' . $currentMonthEnd->format('Y-m-d'),
                        'nextMonth' => $nextMonthStart->format('Y-m-d') . ' to ' . $nextMonthEnd->format('Y-m-d')
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Follow-up Dashboard Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'stats' => [
                    'lastMonth' => 0,
                    'currentMonth' => 0,
                    'nextMonth' => 0,
                    'total' => 0
                ],
                'data' => [
                    'lastMonth' => [],
                    'currentMonth' => [],
                    'nextMonth' => []
                ]
            ], 500);
        }
    }
    
    /**
     * Save quick follow-up note
     */
    public function saveFollowUpNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'policyId' => 'required|integer',
            'customerName' => 'required|string',
            'phone' => 'required|string',
            'note' => 'required|string',
            'status' => 'required|string',
            'nextFollowupDate' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::create([
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'policy_type' => 'Renewal',
            'followup_type' => 'Renewal',
            'followup_date' => $request->nextFollowupDate ?? now()->addDays(7)->toDateString(),
            'followup_time' => '10:00:00',
            'status' => $request->status,
            'agent_name' => auth()->user()->name ?? 'Self',
            'notes' => $request->note
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note saved successfully!',
            'followup' => $followup
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