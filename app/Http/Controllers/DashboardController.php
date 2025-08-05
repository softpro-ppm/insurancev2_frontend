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
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();
        
        // Policy statistics
        $totalPolicies = Policy::count();
        $activePolicies = Policy::where('status', 'Active')->count();
        $expiredPolicies = Policy::where('status', 'Expired')->count();
        $pendingRenewals = Renewal::where('status', 'Pending')->count();
        
        // Monthly statistics
        $monthlyPolicies = Policy::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        
        $monthlyPremium = Policy::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('premium');
        
        $monthlyRevenue = Policy::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('revenue');
        
        // Yearly statistics
        $yearlyPolicies = Policy::whereYear('created_at', $currentYear->year)->count();
        $yearlyPremium = Policy::whereYear('created_at', $currentYear->year)->sum('premium');
        $yearlyRevenue = Policy::whereYear('created_at', $currentYear->year)->sum('revenue');
        
        // Monthly renewals
        $monthlyRenewals = Renewal::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        
        // Policy type distribution
        $policyTypes = Policy::selectRaw('policy_type, COUNT(*) as count')
            ->groupBy('policy_type')
            ->get()
            ->pluck('count', 'policy_type')
            ->toArray();
        
        // Monthly chart data (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartData[] = [
                'month' => $date->format('M'),
                'premium' => Policy::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('premium'),
                'revenue' => Policy::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('revenue'),
                'policies' => Policy::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count()
            ];
        }
        
        return response()->json([
            'stats' => [
                'totalPolicies' => $totalPolicies,
                'activePolicies' => $activePolicies,
                'expiredPolicies' => $expiredPolicies,
                'pendingRenewals' => $pendingRenewals,
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
                    'policyType' => $policy->policy_type,
                    'companyName' => $policy->company_name,
                    'endDate' => $policy->end_date->format('Y-m-d'),
                    'premium' => $policy->premium,
                    'status' => $policy->status,
                    'createdAt' => $policy->created_at->format('Y-m-d')
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