<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AgentLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
    public function store(AgentLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Manually store agent authentication in session
        $request->session()->put('agent_authenticated', true);
        $request->session()->put('agent_id', Auth::guard('agent')->id());

        return redirect()->intended(route('agent.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated agent session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('agent')->logout();

        // Clear manual session data
        $request->session()->forget('agent_authenticated');
        $request->session()->forget('agent_id');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/agent/login');
    }
}
