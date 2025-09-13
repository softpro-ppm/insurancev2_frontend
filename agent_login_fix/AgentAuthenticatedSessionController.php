<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AgentAuthenticatedSessionController extends Controller
{
    /**
     * Display the agent login view.
     */
    public function create(): View
    {
        return view('auth.agent-login');
    }

    /**
     * Handle an incoming agent authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to authenticate the agent
        $agent = Agent::where('email', $request->email)->first();
        
        if ($agent && password_verify($request->password, $agent->password)) {
            // Manually log in the agent
            Auth::guard('agent')->login($agent);
            
            $request->session()->regenerate();
            
            return redirect()->intended(route('agent.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated agent session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('agent')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}