<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCsrfForTesting
{
    public function handle(Request $request, Closure $next)
    {
        // Skip CSRF verification for testing
        return $next($request);
    }
}