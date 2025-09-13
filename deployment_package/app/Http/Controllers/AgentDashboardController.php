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
        
        // Get agent's policies
        $policies = Policy::where('phone', $agent->phone)->get();
        
        // Get agent's renewals
        $renewals = Renewal::where('agent_name', $agent->name)->get();
        
        // Get agent's followups
        $followups = Followup::where('agent_name', $agent->name)->get();
        
        // Calculate stats
        $totalPolicies = $policies->count();
        $activePolicies = $policies->where('status', 'Active')->count();
        $expiringSoon = $policies->filter(function($policy) {
            $endDate = \Carbon\Carbon::parse($policy->endDate);
            return $endDate->diffInDays(now()) <= 30 && $endDate->isFuture();
        })->count();
        
        $totalPremium = $policies->sum('premium');
        $totalRevenue = $policies->sum('revenue');
        
        return view('agent.dashboard', compact(
            'agent',
            'policies',
            'renewals', 
            'followups',
            'totalPolicies',
            'activePolicies',
            'expiringSoon',
            'totalPremium',
            'totalRevenue'
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
