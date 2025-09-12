<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AgentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check both Laravel's guard and manual session storage
        if (!Auth::guard('agent')->check() && !$request->session()->get('agent_authenticated')) {
            return redirect()->route('agent.login');
        }

        // If manual session exists but guard doesn't, restore the authentication
        if ($request->session()->get('agent_authenticated') && !Auth::guard('agent')->check()) {
            $agentId = $request->session()->get('agent_id');
            if ($agentId) {
                $agent = \App\Models\Agent::find($agentId);
                if ($agent) {
                    Auth::guard('agent')->login($agent);
                }
            }
        }

        return $next($request);
    }
}
