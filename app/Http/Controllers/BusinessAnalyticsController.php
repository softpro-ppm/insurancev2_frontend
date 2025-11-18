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
        
        // Build query with optional date filters - use policy_issue_date instead of created_at
        $query = Policy::query();
        
        if ($startDate && $endDate) {
            \Log::info('Business Analytics - Filtering by dates', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            $query->where(function($q) use ($startDate, $endDate) {
                $q->where(function($subQuery) use ($startDate, $endDate) {
                    // Policies with policy_issue_date in range
                    $subQuery->whereNotNull('policy_issue_date')
                             ->whereBetween('policy_issue_date', [$startDate, $endDate]);
                })->orWhere(function($subQuery) use ($startDate, $endDate) {
                    // Fallback to created_at if policy_issue_date is null
                    $subQuery->whereNull('policy_issue_date')
                             ->whereBetween('created_at', [$startDate, $endDate]);
                });
            });
        } else {
            \Log::info('Business Analytics - No date filter applied');
        }
        
        $policyCount = $query->count();
        \Log::info('Business Analytics - Policies found', ['count' => $policyCount]);
        
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
            
            $previousPolicies = Policy::where(function($q) use ($prevStart, $prevEnd) {
                $q->whereBetween('policy_issue_date', [$prevStart, $prevEnd])
                  ->orWhere(function($subQ) use ($prevStart, $prevEnd) {
                      $subQ->whereNull('policy_issue_date')
                           ->whereBetween('created_at', [$prevStart, $prevEnd]);
                  });
            })->get();
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
        $period = $request->get('period', 'year'); // 6months, 12months, year, custom
        
        // Determine date range based on period
        $today = Carbon::now();
        switch ($period) {
            case '6months':
                $startDate = $today->copy()->subMonths(6)->startOfMonth();
                break;
            case 'year':
                // Financial Year: April 1 to March 31
                $currentMonth = $today->month; // 1-12
                if ($currentMonth >= 4) {
                    // April to December: Current financial year started April 1 of current year
                    $startDate = Carbon::create($today->year, 4, 1)->startOfDay();
                } else {
                    // January to March: Current financial year started April 1 of previous year
                    $startDate = Carbon::create($today->year - 1, 4, 1)->startOfDay();
                }
                break;
            case 'all':
                $minDate = Policy::whereNotNull('policy_issue_date')->min('policy_issue_date');
                if (!$minDate) {
                    $minDate = Policy::min('created_at');
                }
                $startDate = $minDate ? Carbon::parse($minDate)->startOfMonth() : $today->copy()->subYear();
                break;
            default: // 12months
                $startDate = $today->copy()->subMonths(12)->startOfMonth();
        }
        
        $endDate = $today->copy()->endOfMonth();
        
        // Get monthly aggregated data - use policy_issue_date, fallback to created_at
        $query = Policy::query();
        
        // Only apply date filter if not "all" period
        if ($period !== 'all' && $startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->where(function($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereNotNull('policy_issue_date')
                             ->whereBetween('policy_issue_date', [$startDate, $endDate]);
                })->orWhere(function($subQ) use ($startDate, $endDate) {
                    $subQ->whereNull('policy_issue_date')
                         ->whereBetween('created_at', [$startDate, $endDate]);
                });
            });
        }
        
        $monthlyData = $query
            ->select(
                DB::raw('DATE_FORMAT(COALESCE(policy_issue_date, created_at), "%Y-%m") as month'),
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
    public function getPolicyDistribution(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = Policy::select('policy_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout');
        
        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('policy_issue_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->whereNull('policy_issue_date')
                           ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            });
        }
        
        $distribution = $query->groupBy('policy_type')->get();
        
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
    public function getBusinessTypePerformance(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = Policy::select('business_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout');
        
        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('policy_issue_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->whereNull('policy_issue_date')
                           ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            });
        }
        
        $performance = $query->groupBy('business_type')->get();
        
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
    public function getAgentPerformance(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $agentQuery = Policy::where('business_type', 'Agent');
        $selfQuery = Policy::where('business_type', 'Self');
        
        // Apply date filter if provided
        if ($startDate && $endDate) {
            $dateFilter = function($q) use ($startDate, $endDate) {
                $q->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('policy_issue_date', [$startDate, $endDate])
                          ->orWhere(function($subQ) use ($startDate, $endDate) {
                              $subQ->whereNull('policy_issue_date')
                                   ->whereBetween('created_at', [$startDate, $endDate]);
                          });
                });
            };
            
            $agentQuery->where($dateFilter);
            $selfQuery->where($dateFilter);
        }
        
        $agentStats = $agentQuery
            ->select('agent_name')
            ->selectRaw('COUNT(*) as policy_count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout')
            ->groupBy('agent_name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        // Include Self business
        $selfStats = $selfQuery
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
    public function getTopCompanies(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = Policy::select('company_name')
            ->selectRaw('COUNT(*) as policy_count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(payout) as total_payout');
        
        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('policy_issue_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->whereNull('policy_issue_date')
                           ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            });
        }
        
        $companies = $query->groupBy('company_name')
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
    public function getProfitabilityBreakdown(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = Policy::select('policy_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(premium) as total_premium')
            ->selectRaw('SUM(customer_paid_amount) as total_customer_paid')
            ->selectRaw('SUM(payout) as total_payout')
            ->selectRaw('SUM(revenue) as total_revenue');
        
        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('policy_issue_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->whereNull('policy_issue_date')
                           ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            });
        }
        
        $breakdown = $query->groupBy('policy_type')->get();
        
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
    public function getMonthlyGrowth(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Build query
        $query = Policy::query();
        
        // Only apply date filter if dates are provided
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
            
            $query->where(function($q) use ($startDate, $endDate) {
                $q->where(function($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereNotNull('policy_issue_date')
                             ->whereBetween('policy_issue_date', [$startDate, $endDate]);
                })->orWhere(function($subQ) use ($startDate, $endDate) {
                    $subQ->whereNull('policy_issue_date')
                         ->whereBetween('created_at', [$startDate, $endDate]);
                });
            });
        }
        // If no dates provided, get all data (for "All Time" option)
        
        // Get monthly data - use policy_issue_date, fallback to created_at
        $monthlyData = $query
            ->select(
                DB::raw('DATE_FORMAT(COALESCE(policy_issue_date, created_at), "%Y-%m") as month'),
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

