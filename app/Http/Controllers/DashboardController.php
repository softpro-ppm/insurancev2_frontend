<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\Renewal;
use Illuminate\Support\Facades\Schema;
use App\Models\Agent;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        // Simple cache to reduce DB hits on shared hosting
        $cacheKey = 'dashboard_stats_' . ($request->get('period', 'financial_year'));
        $cached = cache()->get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }
        $now = Carbon::now();
        $currentMonth = $now->copy()->startOfMonth();
        // Financial Year start: Apr 1 of current year if month >= 4 else Apr 1 of previous year
        $fyStart = $now->month >= 4
            ? Carbon::create($now->year, 4, 1)->startOfDay()
            : Carbon::create($now->year - 1, 4, 1)->startOfDay();
        $fyEnd = $fyStart->copy()->addYear()->subDay()->endOfDay();
        
        // Policy statistics - Simple and compatible queries
        $totalPolicies = Policy::count();
        $activePolicies = Policy::where('status', 'Active')->count();
        $expiredPolicies = Policy::where('status', 'Expired')->count();
        $pendingRenewals = Schema::hasTable('renewals')
            ? Renewal::where('status', 'Pending')->count()
            : 0;
        
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
            // Monthly renewed policies (those that have been processed/renewed)
$monthlyRenewed = Policy::whereMonth('end_date', $currentMonth->month)
->whereYear('end_date', $currentMonth->year)
->where('status', 'Renewed')
->count();
        
        // Policy type distribution (last 12 months)
        $oneYearAgo = $now->copy()->subMonths(11)->startOfMonth();
        $policyTypes = Policy::selectRaw('policy_type, COUNT(*) as count')
            ->where('start_date', '>=', $oneYearAgo)
            ->groupBy('policy_type')
            ->get()
            ->pluck('count', 'policy_type')
            ->toArray();
        
        // Chart data based on selected period
        $period = $request->get('period', 'financial_year');
        $chartData = $this->getChartDataForPeriod($period, $now);
        
        // Log chart data for debugging
        \Log::info('Dashboard Chart Data Generated', [
            'period' => $period,
            'chartDataCount' => count($chartData),
            'hasData' => collect($chartData)->sum('premium') > 0 || collect($chartData)->sum('revenue') > 0 || collect($chartData)->sum('policies') > 0,
            'chartData' => $chartData
        ]);

        $payload = [
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
                'monthlyRenewed' => $monthlyRenewed,
                'yearlyPolicies' => $yearlyPolicies,
                'yearlyPremium' => $yearlyPremium,
                'yearlyRevenue' => $yearlyRevenue,
                // Add total counts for main dashboard cards
                'totalPremium' => Policy::sum('premium'),
                'totalRevenue' => Policy::sum('revenue'),
                'totalRenewals' => Policy::where('end_date', '<=', $now->copy()->addDays(30))
                    ->where('end_date', '>=', $now->toDateString())
                    ->where('status', 'Active')
                    ->count(),
            ],
            'policyTypes' => $policyTypes,
            'chartData' => $chartData
        ];
        cache()->put($cacheKey, $payload, 60); // cache 60 seconds
        return response()->json($payload);
    }

    /**
     * Get chart data based on selected period
     */
    private function getChartDataForPeriod($period, $now)
    {
        $chartData = [];
        
        switch ($period) {
            case 'current_month':
                // Current month - show daily data for last 30 days
                for ($i = 29; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $chartData[] = [
                        'month' => $date->format('d'),
                        'premium' => Policy::whereDate('start_date', $date->toDateString())->sum('premium'),
                        'revenue' => Policy::whereDate('start_date', $date->toDateString())->sum('revenue'),
                        'policies' => Policy::whereDate('start_date', $date->toDateString())->count()
                    ];
                }
                break;
                
            case 'current_quarter':
                // Current quarter - show monthly data for last 3 months
                for ($i = 2; $i >= 0; $i--) {
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
                break;
                
            case 'financial_year':
            default:
                // Financial year - show last 12 months
                for ($i = 11; $i >= 0; $i--) {
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
                break;
        }
        
        return $chartData;
    }
    
    /**
     * Get recent policies for dashboard (last 30 days)
     */
public function getRecentPolicies()
    {
        // Get policies from the last 30 days only
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        $recentPolicies = Policy::where('start_date', '>=', $thirtyDaysAgo)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($policy) {
                return [
                    'id' => $policy->id,
                    'customerName' => $policy->customer_name,
                    'phone' => $policy->phone,
                    'email' => $policy->email,
                    'policyType' => $policy->policy_type,
                    'vehicleNumber' => $policy->vehicle_number,
                    'vehicleType' => $policy->vehicle_type,
                    'companyName' => $policy->company_name,
                    'insuranceType' => $policy->insurance_type,
                    'startDate' => $policy->start_date->format('d-m-Y'),
                    'endDate' => $policy->end_date->format('d-m-Y'),
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
                    'customerName' => $policy->customer_name,
                    'phone' => $policy->phone,
                    'endDate' => $policy->end_date->format('Y-m-d'),
                    'daysUntilExpiry' => Carbon::now()->diffInDays($policy->end_date, false)
                ];
            });
        
        return response()->json(['expiringPolicies' => $expiringPolicies]);
    }
} 