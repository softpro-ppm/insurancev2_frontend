<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\Renewal;
use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $agent = Auth::guard('agent')->user();
        
        // Get current month date range
        $currentMonth = now()->format('Y-m');
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        // 1. Total Policies = policies with start date in current month
        $totalPoliciesCurrentMonth = Policy::where('phone', $agent->phone)
            ->whereBetween('startDate', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->count();
        
        // 2. Total Renewals = policies with end date in current month (expiring this month)
        $totalRenewalsCurrentMonth = Policy::where('phone', $agent->phone)
            ->whereBetween('endDate', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->count();
        
        // 3. Total Renewed = policies that were updated/modified in current month (renewed)
        $totalRenewedCurrentMonth = Policy::where('phone', $agent->phone)
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->where('updated_at', '!=', 'created_at') // Exclude newly created policies
            ->count();
        
        // 4. Total Premium = premium of policies with start date in current month
        $totalPremiumCurrentMonth = Policy::where('phone', $agent->phone)
            ->whereBetween('startDate', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->sum('premium');
        
        // Get policies for current month table (start date in current month)
        $currentMonthPolicies = Policy::where('phone', $agent->phone)
            ->whereBetween('startDate', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('startDate', 'desc')
            ->get();
        
        return view('agent.dashboard', compact(
            'agent',
            'currentMonthPolicies',
            'totalPoliciesCurrentMonth',
            'totalRenewalsCurrentMonth',
            'totalRenewedCurrentMonth',
            'totalPremiumCurrentMonth',
            'currentMonth'
        ));
    }
    
    public function policies()
    {
        $agent = Auth::guard('agent')->user();
        $policies = Policy::where('phone', $agent->phone)->paginate(25);
        
        return view('agent.policies', compact('agent', 'policies'));
    }
    
    public function renewals()
    {
        $agent = Auth::guard('agent')->user();
        $renewals = Renewal::where('agent_name', $agent->name)->paginate(25);
        
        return view('agent.renewals', compact('agent', 'renewals'));
    }
    
    public function followups()
    {
        $agent = Auth::guard('agent')->user();
        $followups = Followup::where('agent_name', $agent->name)->paginate(25);
        
        return view('agent.followups', compact('agent', 'followups'));
    }
}
