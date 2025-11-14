<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        try {
            $totalPolicies = Policy::count();
            $activePolicies = Policy::where("status", "Active")->count();
            $expiredPolicies = Policy::where("status", "Expired")->count();
            
            $payload = [
                "stats" => [
                    "totalPolicies" => $totalPolicies,
                    "activePolicies" => $activePolicies,
                    "expiredPolicies" => $expiredPolicies,
                    "totalPremium" => Policy::sum("premium"),
                    "totalRevenue" => Policy::sum("revenue"),
                ]
            ];
            
            return response()->json($payload);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function getRecentPolicies()
    {
        try {
            $recentPolicies = Policy::latest()->limit(10)->get();
            return response()->json(["recentPolicies" => $recentPolicies]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function getExpiringPolicies()
    {
        try {
            $expiringPolicies = Policy::where("end_date", "<=", Carbon::now()->addDays(30))->get();
            return response()->json(["expiringPolicies" => $expiringPolicies]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
}