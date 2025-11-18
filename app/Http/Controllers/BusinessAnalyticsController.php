<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessAnalyticsController extends Controller
{
    /**
     * Display the business analytics page
     */
    public function index()
    {
        return view('business-analytics');
    }

    /**
     * Get business overview KPIs
     */
    public function getOverview(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Build query with optional date filters
        $query = Policy::query();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        // Get all policies for current period
        $currentPolicies = $query->get();
        
        // Calculate KPIs
        $totalPremium = $currentPolicies->sum('premium');
        $totalRevenue = $currentPolicies->sum('revenue');
        $totalPayout = $currentPolicies->sum('payout');
        $totalCustomerPaid = $currentPolicies->sum('customer_paid_amount');
        $totalPolicies = $currentPolicies->count();
        $activePolicies = $currentPolicies->where('status', 'Active')->count();
        
        // Calculate profit margin (Revenue = Profit from customer/agent, so Revenue / Premium * 100)
        // Revenue already represents profit earned from customer/agent
        $profitMargin = $totalPremium > 0 ? ($totalRevenue / $totalPremium) * 100 : 0;
        
        // Calculate average policy value
        $avgPolicyValue = $totalPolicies > 0 ? $totalPremium / $totalPolicies : 0;
        
        // Get previous period data for comparison (same date range, but previous period)
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $duration = $start->diffInDays($end);
            
            $prevStart = $start->copy()->subDays($duration + 1);
            $prevEnd = $start->copy()->subDay();
            
            $previousPolicies = Policy::whereBetween('created_at', [$prevStart, $prevEnd])->get();
            $previousRevenue = $previousPolicies->sum('revenue');
            $previousPolicies = $previousPolicies->count();
            
            // Calculate growth rates
            $revenueGrowth = $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
            $policyGrowth = $previousPolicies > 0 ? (($totalPolicies - $previousPolicies) / $previousPolicies) * 100 : 0;
        } else {
            $revenueGrowth = 0;
            $policyGrowth = 0;
        }
        
        // Monthly recurring revenue (approximate)
        $mrr = $activePolicies > 0 ? ($totalPremium / 12) : 0;
        
        return response()->json([
            'kpis' => [
                'totalPremium' => round($totalPremium, 2),
                'totalRevenue' => round($totalRevenue, 2),
                'totalPayout' => round($totalPayout, 2),
                'totalCustomerPaid' => round($totalCustomerPaid, 2),
                'profitMargin' => round($profitMargin, 2),
                'totalPolicies' => $totalPolicies,
                'activePolicies' => $activePolicies,
                'avgPolicyValue' => round($avgPolicyValue, 2),
                'monthlyRecurringRevenue' => round($mrr, 2),
                'revenueGrowth' => round($revenueGrowth, 2),
                'policyGrowth' => round($policyGrowth, 2),
            ]
        ]);
    }

    /**
     * Get revenue trend data for charts
     */
    public function getRevenueTrend(Request $request)
    {
        $period = $request->get('period', '12months'); // 6months, 12months, year, custom
        
        // Determine date range based on period
        switch ($period) {
            case '6months':
                $startDate = Carbon::now()->subMonths(6)->startOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'all':
                $startDate = Policy::min('created_at') ? Carbon::parse(Policy::min('created_at'))->startOfMonth() : Carbon::now()->subYear();
                break;
            default: // 12months
                $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        }
        
        $endDate = Carbon::now()->endOfMonth();
        
        // Get monthly aggregated data
        $monthlyData = Policy::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(premium) as total_premium'),
                DB::raw('SUM(revenue) as total_revenue'),
                DB::raw('SUM(payout) as total_payout'),
                DB::raw('SUM(customer_paid_amount) as total_customer_paid'),
                DB::raw('COUNT(*) as policy_count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
        // Calculate net profit for each month
        $chartData = $monthlyData->map(function ($item) {
            $netProfit = $item->total_revenue - $item->total_payout;
            return [
                'month' => Carbon::parse($item->month . '-01')->format('M Y'),
                'premium' => round($item->total_premium, 2),
                'revenue' => round($item->total_revenue, 2),
                'payout' => round($item->total_payout, 2),
                'netProfit' => round($netProfit, 2),
                'policyCount' => $item->policy_count,
            ];
        });
        
        return response()->json([
            'chartData' => $chartData
        ]);
    }

    /**
     * Get policy type distribution
     */
    public function getPolicyDistribution()
    {
        $distribution = Policy::select('policy_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout')
            ->groupBy('policy_type')
            ->get();
        
        return response()->json([
            'distribution' => $distribution->map(function ($item) {
                // Revenue = Profit from customer/agent
                $profitMargin = $item->total_premium > 0 ? ($item->total_revenue / $item->total_premium) * 100 : 0;
                return [
                    'type' => $item->policy_type,
                    'count' => $item->count,
                    'premium' => round($item->total_premium, 2),
                    'revenue' => round($item->total_revenue, 2),
                    'profitMargin' => round($profitMargin, 2),
                ];
            })
        ]);
    }

    /**
     * Get business type performance (Self vs Agent)
     */
    public function getBusinessTypePerformance()
    {
        $performance = Policy::select('business_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout')
            ->groupBy('business_type')
            ->get();
        
        return response()->json([
            'performance' => $performance->map(function ($item) {
                $avgPolicyValue = $item->count > 0 ? $item->total_premium / $item->count : 0;
                // Revenue = Profit from customer/agent
                $profitMargin = $item->total_premium > 0 ? ($item->total_revenue / $item->total_premium) * 100 : 0;
                
                return [
                    'businessType' => $item->business_type,
                    'count' => $item->count,
                    'premium' => round($item->total_premium, 2),
                    'revenue' => round($item->total_revenue, 2),
                    'payout' => round($item->total_payout, 2),
                    'avgPolicyValue' => round($avgPolicyValue, 2),
                    'profitMargin' => round($profitMargin, 2),
                ];
            })
        ]);
    }

    /**
     * Get agent performance statistics
     */
    public function getAgentPerformance()
    {
        $agentStats = Policy::where('business_type', 'Agent')
            ->select('agent_name')
            ->selectRaw('COUNT(*) as policy_count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout')
            ->groupBy('agent_name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        // Include Self business
        $selfStats = Policy::where('business_type', 'Self')
            ->selectRaw('COUNT(*) as policy_count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout')
            ->first();
        
        $performance = $agentStats->map(function ($agent) {
            $avgPolicyValue = $agent->policy_count > 0 ? $agent->total_premium / $agent->policy_count : 0;
            // Revenue = Profit from customer/agent
            $profitMargin = $agent->total_premium > 0 ? ($agent->total_revenue / $agent->total_premium) * 100 : 0;
            
            return [
                'name' => $agent->agent_name,
                'policyCount' => $agent->policy_count,
                'premium' => round($agent->total_premium, 2),
                'revenue' => round($agent->total_revenue, 2),
                'payout' => round($agent->total_payout, 2),
                'avgPolicyValue' => round($avgPolicyValue, 2),
                'profitMargin' => round($profitMargin, 2),
            ];
        })->toArray();
        
        // Add Self business
        if ($selfStats && $selfStats->policy_count > 0) {
            $avgPolicyValue = $selfStats->total_premium / $selfStats->policy_count;
            // Revenue = Profit from customer/agent
            $profitMargin = $selfStats->total_premium > 0 ? ($selfStats->total_revenue / $selfStats->total_premium) * 100 : 0;
            
            array_unshift($performance, [
                'name' => 'Self',
                'policyCount' => $selfStats->policy_count,
                'premium' => round($selfStats->total_premium, 2),
                'revenue' => round($selfStats->total_revenue, 2),
                'payout' => round($selfStats->total_payout, 2),
                'avgPolicyValue' => round($avgPolicyValue, 2),
                'profitMargin' => round($profitMargin, 2),
            ]);
        }
        
        return response()->json([
            'agents' => $performance
        ]);
    }

    /**
     * Get top performing insurance companies
     */
    public function getTopCompanies()
    {
        $companies = Policy::select('company_name')
            ->selectRaw('COUNT(*) as policy_count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout')
            ->groupBy('company_name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'companies' => $companies->map(function ($company) {
                // Revenue = Profit from customer/agent
                $profitMargin = $company->total_premium > 0 ? ($company->total_revenue / $company->total_premium) * 100 : 0;
                
                return [
                    'name' => $company->company_name,
                    'policyCount' => $company->policy_count,
                    'premium' => round($company->total_premium, 2),
                    'revenue' => round($company->total_revenue, 2),
                    'profitMargin' => round($profitMargin, 2),
                ];
            })
        ]);
    }

    /**
     * Get profitability breakdown by policy type
     */
    public function getProfitabilityBreakdown()
    {
        $breakdown = Policy::select('policy_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(customer_paid_amount) as total_customer_paid')
            ->selectRaw('SUM(payout) as total_payout')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->groupBy('policy_type')
            ->get();
        
        $total = [
            'count' => 0,
            'premium' => 0,
            'customerPaid' => 0,
            'payout' => 0,
            'revenue' => 0,
        ];
        
        $data = $breakdown->map(function ($item) use (&$total) {
            // Revenue = Profit from customer/agent
            $profitMargin = $item->total_premium > 0 ? ($item->total_revenue / $item->total_premium) * 100 : 0;
            
            $total['count'] += $item->count;
            $total['premium'] += $item->total_premium;
            $total['customerPaid'] += $item->total_customer_paid;
            $total['payout'] += $item->total_payout;
            $total['revenue'] += $item->total_revenue;
            
            return [
                'type' => $item->policy_type,
                'count' => $item->count,
                'premium' => round($item->total_premium, 2),
                'customerPaid' => round($item->total_customer_paid, 2),
                'payout' => round($item->total_payout, 2),
                'revenue' => round($item->total_revenue, 2),
                'profitMargin' => round($profitMargin, 2),
            ];
        });
        
        // Calculate overall profit margin (Revenue = Profit)
        $total['profitMargin'] = $total['premium'] > 0 ? ($total['revenue'] / $total['premium']) * 100 : 0;
        
        return response()->json([
            'breakdown' => $data,
            'total' => [
                'count' => $total['count'],
                'premium' => round($total['premium'], 2),
                'customerPaid' => round($total['customerPaid'], 2),
                'payout' => round($total['payout'], 2),
                'revenue' => round($total['revenue'], 2),
                'profitMargin' => round($total['profitMargin'], 2),
            ]
        ]);
    }

    /**
     * Get monthly growth comparison
     */
    public function getMonthlyGrowth()
    {
        // Get data for last 12 months
        $monthlyData = Policy::where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(revenue) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
        // Calculate month-over-month growth
        $growthData = [];
        $previousCount = 0;
        $previousRevenue = 0;
        
        foreach ($monthlyData as $index => $data) {
            $countGrowth = $previousCount > 0 ? (($data->count - $previousCount) / $previousCount) * 100 : 0;
            $revenueGrowth = $previousRevenue > 0 ? (($data->revenue - $previousRevenue) / $previousRevenue) * 100 : 0;
            
            $growthData[] = [
                'month' => Carbon::parse($data->month . '-01')->format('M Y'),
                'policyCount' => $data->count,
                'revenue' => round($data->revenue, 2),
                'countGrowth' => round($countGrowth, 2),
                'revenueGrowth' => round($revenueGrowth, 2),
            ];
            
            $previousCount = $data->count;
            $previousRevenue = $data->revenue;
        }
        
        return response()->json([
            'growthData' => $growthData
        ]);
    }

    /**
     * Get renewal opportunities and statistics
     */
    public function getRenewalOpportunities()
    {
        $today = Carbon::now();
        
        // Policies expiring in next 30 days
        $next30Days = Policy::where('status', 'Active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(30)])
            ->get();
        
        // Policies expiring in 31-60 days
        $next60Days = Policy::where('status', 'Active')
            ->whereBetween('end_date', [$today->copy()->addDays(31), $today->copy()->addDays(60)])
            ->get();
        
        // Policies expiring in 61-90 days
        $next90Days = Policy::where('status', 'Active')
            ->whereBetween('end_date', [$today->copy()->addDays(61), $today->copy()->addDays(90)])
            ->get();
        
        // Calculate estimated revenue from renewals
        $estimated30Days = $next30Days->sum('premium');
        $estimated60Days = $next60Days->sum('premium');
        $estimated90Days = $next90Days->sum('premium');
        
        // Historical renewal rate (policies that were renewed vs. expired)
        $totalEligibleForRenewal = Policy::where('end_date', '<', $today)->count();
        $totalRenewed = Policy::has('versions')->count(); // Policies with versions (renewals)
        $renewalRate = $totalEligibleForRenewal > 0 ? ($totalRenewed / $totalEligibleForRenewal) * 100 : 0;
        
        return response()->json([
            'opportunities' => [
                'next30Days' => [
                    'count' => $next30Days->count(),
                    'estimatedRevenue' => round($estimated30Days, 2),
                ],
                'next60Days' => [
                    'count' => $next60Days->count(),
                    'estimatedRevenue' => round($estimated60Days, 2),
                ],
                'next90Days' => [
                    'count' => $next90Days->count(),
                    'estimatedRevenue' => round($estimated90Days, 2),
                ],
                'historicalRenewalRate' => round($renewalRate, 2),
            ]
        ]);
    }
}

