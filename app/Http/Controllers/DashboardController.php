<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\Renewal;
use App\Models\Agent;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        $now = Carbon::now();
        $currentMonth = $now->copy()->startOfMonth();
        // Financial Year start: Apr 1 of current year if month >= 4 else Apr 1 of previous year
        $fyStart = $now->month >= 4
            ? Carbon::create($now->year, 4, 1)->startOfDay()
            : Carbon::create($now->year - 1, 4, 1)->startOfDay();
        $fyEnd = $fyStart->copy()->addYear()->subDay()->endOfDay();
        
        // Policy statistics
        $totalPolicies = Policy::count();
        $activePolicies = Policy::where('status', 'Active')->count();
        $expiredPolicies = Policy::where('status', 'Expired')->count();
        $pendingRenewals = Renewal::where('status', 'Pending')->count();
        
        // Monthly statistics (based on policy START DATE)
        $monthlyPolicies = Policy::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->count();
        
        $monthlyPremium = Policy::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->sum('premium');
        
        $monthlyRevenue = Policy::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->sum('revenue');
        
        // Financial Year statistics (based on policy START DATE)
        $yearlyPolicies = Policy::whereBetween('start_date', [$fyStart, $fyEnd])->count();
        $yearlyPremium = Policy::whereBetween('start_date', [$fyStart, $fyEnd])->sum('premium');
        $yearlyRevenue = Policy::whereBetween('start_date', [$fyStart, $fyEnd])->sum('revenue');
        
        // Monthly renewals (based on policy END DATE)
        $monthlyRenewals = Policy::whereMonth('end_date', $currentMonth->month)
            ->whereYear('end_date', $currentMonth->year)
            ->count();
        
        // Policy type distribution
        $policyTypes = Policy::selectRaw('policy_type, COUNT(*) as count')
            ->groupBy('policy_type')
            ->get()
            ->pluck('count', 'policy_type')
            ->toArray();
        
        // Monthly chart data (last 6 months) based on START DATE
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $chartData[] = [
                'month' => $date->format('M'),
                'premium' => Policy::whereMonth('start_date', $date->month)
                    ->whereYear('start_date', $date->year)
                    ->sum('premium'),
                'revenue' => Policy::whereMonth('start_date', $date->month)
                    ->whereYear('start_date', $date->year)
                    ->sum('revenue'),
                'policies' => Policy::whereMonth('start_date', $date->month)
                    ->whereYear('start_date', $date->year)
                    ->count()
            ];
        }
        
        return response()->json([
            'stats' => [
                'totalPolicies' => $totalPolicies,
                'activePolicies' => $activePolicies,
                'expiredPolicies' => $expiredPolicies,
                // Pending renewals for current month: end_date in current month and today not passed
                'pendingRenewals' => Policy::whereMonth('end_date', $currentMonth->month)
                    ->whereYear('end_date', $currentMonth->year)
                    ->whereDate('end_date', '>=', $now->toDateString())
                    ->count(),
                'monthlyPolicies' => $monthlyPolicies,
                'monthlyPremium' => $monthlyPremium,
                'monthlyRevenue' => $monthlyRevenue,
                'monthlyRenewals' => $monthlyRenewals,
                'yearlyPolicies' => $yearlyPolicies,
                'yearlyPremium' => $yearlyPremium,
                'yearlyRevenue' => $yearlyRevenue,
            ],
            'policyTypes' => $policyTypes,
            'chartData' => $chartData
        ]);
    }
    
    /**
     * Get recent policies for dashboard
     */
    public function getRecentPolicies()
    {
        $recentPolicies = Policy::latest()
            ->take(10)
            ->get()
            ->map(function ($policy) {
                return [
                    'id' => $policy->id,
                    'policyNumber' => $policy->policy_number,
                    'customerName' => $policy->customer_name,
                    'phone' => $policy->phone,
                    'email' => $policy->email,
                    'policyType' => $policy->policy_type,
                    'vehicleNumber' => $policy->vehicle_number,
                    'vehicleType' => $policy->vehicle_type,
                    'companyName' => $policy->company_name,
                    'insuranceType' => $policy->insurance_type,
                    'startDate' => $policy->start_date->format('Y-m-d'),
                    'endDate' => $policy->end_date->format('Y-m-d'),
                    'premium' => $policy->premium,
                    'payout' => $policy->payout,
                    'customerPaidAmount' => $policy->customer_paid_amount,
                    'revenue' => $policy->revenue,
                    'status' => $policy->status,
                    'businessType' => $policy->business_type,
                    'agentName' => $policy->agent_name,
                    'createdAt' => $policy->created_at->format('Y-m-d'),
                    'policy_copy_path' => $policy->policy_copy_path,
                    'rc_copy_path' => $policy->rc_copy_path,
                    'aadhar_copy_path' => $policy->aadhar_copy_path,
                    'pan_copy_path' => $policy->pan_copy_path,

                ];
            });
        
        return response()->json(['recentPolicies' => $recentPolicies]);
    }
    
    /**
     * Get expiring policies
     */
    public function getExpiringPolicies()
    {
        $expiringPolicies = Policy::where('end_date', '<=', Carbon::now()->addDays(30))
            ->where('end_date', '>=', Carbon::now())
            ->where('status', 'Active')
            ->get()
            ->map(function ($policy) {
                return [
                    'id' => $policy->id,
                    'policyNumber' => $policy->policy_number,
                    'customerName' => $policy->customer_name,
                    'phone' => $policy->phone,
                    'endDate' => $policy->end_date->format('Y-m-d'),
                    'daysUntilExpiry' => Carbon::now()->diffInDays($policy->end_date, false)
                ];
            });
        
        return response()->json(['expiringPolicies' => $expiringPolicies]);
    }
} 