<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'agent.auth' => \App\Http\Middleware\AgentAuth::class,
        ]);

        // Exclude document operations, version deletion, and follow-ups from CSRF verification
        $middleware->validateCsrfTokens(except: [
            '/api/policies/*/document/*',
            '/api/policy-versions/*',
            '/api/followups/save-note',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
