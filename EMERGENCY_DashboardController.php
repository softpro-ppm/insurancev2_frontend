<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        try {
            return response()->json([
                "stats" => [
                    "totalPolicies" => Policy::count(),
                    "activePolicies" => Policy::where("status", "Active")->count(),
                    "expiredPolicies" => Policy::where("status", "Expired")->count(),
                    "totalPremium" => Policy::sum("premium") ?: "0",
                    "totalRevenue" => Policy::sum("revenue") ?: "0",
                    "totalRenewals" => 0,
                    "monthlyPolicies" => 0,
                    "monthlyPremium" => "0",
                    "monthlyRevenue" => "0",
                    "yearlyPolicies" => 0,
                    "yearlyPremium" => "0",
                    "yearlyRevenue" => "0",
                    "pendingRenewals" => 0,
                    "monthlyRenewals" => 0,
                    "monthlyRenewed" => 0
                ],
                "policyTypes" => [],
                "chartData" => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "stats" => [
                    "totalPolicies" => 0,
                    "activePolicies" => 0,
                    "expiredPolicies" => 0,
                    "totalPremium" => "0",
                    "totalRevenue" => "0",
                    "totalRenewals" => 0,
                    "monthlyPolicies" => 0,
                    "monthlyPremium" => "0",
                    "monthlyRevenue" => "0",
                    "yearlyPolicies" => 0,
                    "yearlyPremium" => "0",
                    "yearlyRevenue" => "0",
                    "pendingRenewals" => 0,
                    "monthlyRenewals" => 0,
                    "monthlyRenewed" => 0
                ],
                "policyTypes" => [],
                "chartData" => []
            ]);
        }
    }
    
    public function getRecentPolicies()
    {
        try {
            return response()->json(["recentPolicies" => []]);
        } catch (\Exception $e) {
            return response()->json(["recentPolicies" => []]);
        }
    }
    
    public function getExpiringPolicies()
    {
        try {
            return response()->json(["expiringPolicies" => []]);
        } catch (\Exception $e) {
            return response()->json(["expiringPolicies" => []]);
        }
    }
}