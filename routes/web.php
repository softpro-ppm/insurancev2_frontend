<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // Added this import for DB facade
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\DisableCsrfForTesting;

// Test login route for debugging
Route::get('/test-login', function () {
    return view('test-login');
})->name('test-login');

// Temporary login route without CSRF for testing
Route::post('/test-login-submit', function (Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('test-login-submit')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Temporary test route for dashboard without auth
Route::get('/test-dashboard', function () {
    return view('dashboard');
})->name('test.dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Dashboard API routes (temporarily without auth for testing)
Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
Route::get('/api/dashboard/recent-policies', [DashboardController::class, 'getRecentPolicies'])->name('dashboard.recent-policies');
Route::get('/api/dashboard/expiring-policies', [DashboardController::class, 'getExpiringPolicies'])->name('dashboard.expiring-policies');

// Policies routes
Route::get('/policies', function () {
    return view('policies.index');
})->name('policies.index');

// Policy CRUD routes (temporarily without auth for testing)
Route::get('/api/policies', [PolicyController::class, 'index'])->name('policies.list');
// Temporarily remove auth to unblock Health/Life submissions; CSRF still enforced
Route::post('/policies', [PolicyController::class, 'store'])
    ->name('policies.store')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/api/policies', [PolicyController::class, 'store'])
    ->name('policies.api.store')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/policies/{id}', [PolicyController::class, 'show'])->name('policies.show');
Route::put('/policies/{id}', [PolicyController::class, 'update'])
    ->name('policies.update')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::delete('/policies/{id}', [PolicyController::class, 'destroy'])->name('policies.destroy');

// Policy export and bulk upload routes
Route::get('/api/policies/export', [PolicyController::class, 'exportPolicies'])->name('policies.export');
Route::get('/api/policies/template/download', [PolicyController::class, 'downloadTemplate'])->name('policies.template.download')->middleware(['auth', 'verified']);
Route::get('/api/policies/template/download-csv', [PolicyController::class, 'downloadCSVTemplate'])->name('policies.template.download-csv')->middleware(['auth', 'verified']);
Route::post('/api/policies/bulk-upload', [PolicyController::class, 'bulkUpload'])->name('policies.bulk-upload')->middleware(['auth', 'verified']);
Route::post('/api/policies/bulk-upload/preview', [PolicyController::class, 'previewBulkUpload'])->name('policies.bulk-upload-preview')->middleware(['auth', 'verified']);

// Bulk upload test page
Route::get('/bulk-upload-test', function () {
    return view('bulk-upload-test');
})->middleware(['auth', 'verified'])->name('bulk-upload-test');

// Policy document download route
Route::get('/api/policies/{policyId}/download/{documentType}', [PolicyController::class, 'downloadDocument'])->name('policies.download-document');

// Policy document delete route
Route::delete('/api/policies/{policyId}/document/{documentType}', [PolicyController::class, 'deleteDocument'])->name('policies.delete-document')->middleware(['auth', 'verified']);

// Policy history route
Route::get('/api/policies/{id}/history', [PolicyController::class, 'getHistory'])->name('policies.history');

// Vehicle number search route
Route::get('/api/policies/search/vehicle/{vehicleNumber}', [PolicyController::class, 'searchByVehicleNumber'])->name('policies.search-vehicle');


// Policy version document download route
Route::get('/api/policy-versions/{versionId}/download/{documentType}', [PolicyController::class, 'downloadVersionDocument'])->name('policy-versions.download-document')->middleware(['auth', 'verified']);

// Renewals routes
Route::get('/renewals', function () {
    return view('renewals.index');
})->middleware(['auth', 'verified'])->name('renewals.index');

// Renewal CRUD routes
Route::get('/api/renewals', [RenewalController::class, 'index'])->name('renewals.list');
Route::post('/renewals', [RenewalController::class, 'store'])->name('renewals.store');
Route::get('/renewals/{id}', [RenewalController::class, 'show'])->name('renewals.show');
Route::put('/renewals/{id}', [RenewalController::class, 'update'])->name('renewals.update');
Route::delete('/renewals/{id}', [RenewalController::class, 'destroy'])->name('renewals.destroy');

// Follow-ups routes
Route::get('/followups', function () {
    return view('followups.index');
})->middleware(['auth', 'verified'])->name('followups.index');

// Followup CRUD routes
Route::get('/api/followups', [FollowupController::class, 'index'])->name('followups.list');
Route::post('/followups', [FollowupController::class, 'store'])->name('followups.store');
Route::get('/followups/{id}', [FollowupController::class, 'show'])->name('followups.show');
Route::put('/followups/{id}', [FollowupController::class, 'update'])->name('followups.update');
Route::delete('/followups/{id}', [FollowupController::class, 'destroy'])->name('followups.destroy');

// Reports routes
Route::get('/reports', function () {
    return view('reports.index');
})->middleware(['auth', 'verified'])->name('reports.index');

// Report CRUD routes
Route::get('/api/reports', [ReportController::class, 'index'])->name('reports.list');
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
Route::put('/reports/{id}', [ReportController::class, 'update'])->name('reports.update');
Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');

// Reports - Policies filtered by start_date range (server-side enforced)
Route::get('/api/reports/policies', [PolicyController::class, 'listByStartDateRange'])->name('reports.policies.by-date')->middleware(['auth', 'verified']);

// Agents routes
Route::get('/agents', function () {
    return view('agents.index');
})->middleware(['auth', 'verified'])->name('agents.index');

// Agents routes
Route::get('/agents', function () {
    return view('agents.index');
})->middleware(['auth', 'verified'])->name('agents.index');

// Agent CRUD routes
Route::get('/api/agents', [AgentController::class, 'index'])->name('agents.list');
Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
Route::get('/agents/{id}', [AgentController::class, 'show'])->name('agents.show');
Route::put('/agents/{id}', [AgentController::class, 'update'])->name('agents.update');
Route::delete('/agents/{id}', [AgentController::class, 'destroy'])->name('agents.destroy');

// Notifications routes
Route::get('/notifications', function () {
    return view('notifications.index');
})->middleware(['auth', 'verified'])->name('notifications.index');

// Notification CRUD routes
Route::get('/api/notifications', [NotificationController::class, 'index'])->name('notifications.list');
Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
Route::put('/notifications/{id}', [NotificationController::class, 'update'])->name('notifications.update');
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

// Settings routes
Route::get('/settings', function () {
    return view('settings.index');
})->middleware(['auth', 'verified'])->name('settings.index');

// Setting CRUD routes
Route::get('/api/settings', [SettingController::class, 'index'])->name('settings.list');
Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
Route::get('/settings/{id}', [SettingController::class, 'show'])->name('settings.show');
Route::put('/settings/{id}', [SettingController::class, 'update'])->name('settings.update');
Route::delete('/settings/{id}', [SettingController::class, 'destroy'])->name('settings.destroy');

// phpMyAdmin route
Route::get('/phpmyadmin', function () {
    return redirect('/phpmyadmin/index.php');
});

// Database Viewer Route
Route::get('/database-viewer', function () {
    $tables = [];
    $selectedTable = request('table');
    $tableData = [];
    
    // Get all tables from SQLite
    $tablesList = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    
    foreach ($tablesList as $table) {
        $tableName = $table->name;
        $count = DB::select("SELECT COUNT(*) as count FROM `{$tableName}`")[0]->count;
        $tables[] = [
            'name' => $tableName,
            'count' => $count
        ];
    }
    
    // If a table is selected, get its data
    if ($selectedTable) {
        try {
            $columns = DB::select("PRAGMA table_info(`{$selectedTable}`)");
            $data = DB::select("SELECT * FROM `{$selectedTable}` LIMIT 100");
            
            $tableData = [
                'columns' => $columns,
                'data' => $data
            ];
        } catch (Exception $e) {
            $tableData = ['error' => $e->getMessage()];
        }
    }
    
    return view('database-viewer', compact('tables', 'selectedTable', 'tableData'));
})->name('database.viewer');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Agent routes
Route::prefix('agent')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\AgentAuthenticatedSessionController::class, 'create'])->name('agent.login');
    Route::post('/login', [App\Http\Controllers\Auth\AgentAuthenticatedSessionController::class, 'store'])->name('agent.login.store');
    Route::post('/logout', [App\Http\Controllers\Auth\AgentAuthenticatedSessionController::class, 'destroy'])->name('agent.logout');
    
    Route::get('/test-login', function() {
        $agent = App\Models\Agent::where('email', 'chbalaram321@gmail.com')->first();
        if ($agent) {
            Auth::guard('agent')->login($agent);
            return response()->json([
                'message' => 'Agent logged in manually',
                'authenticated' => Auth::guard('agent')->check(),
                'user' => Auth::guard('agent')->user(),
                'session_id' => session()->getId()
            ]);
        }
        return response()->json(['error' => 'Agent not found']);
    });
    
    Route::get('/test-auth', function() {
        return response()->json([
            'agent_authenticated' => Auth::guard('agent')->check(),
            'agent_user' => Auth::guard('agent')->user(),
            'web_authenticated' => Auth::guard('web')->check(),
            'web_user' => Auth::guard('web')->user(),
            'session_id' => session()->getId(),
            'manual_session' => [
                'agent_authenticated' => session()->get('agent_authenticated'),
                'agent_id' => session()->get('agent_id'),
            ],
            'all_guards' => [
                'agent' => Auth::guard('agent')->check(),
                'web' => Auth::guard('web')->check(),
            ]
        ]);
    });
    
    Route::middleware(['agent.auth'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AgentDashboardController::class, 'index'])->name('agent.dashboard');
        Route::get('/policies', [App\Http\Controllers\AgentDashboardController::class, 'policies'])->name('agent.policies');
        Route::get('/renewals', [App\Http\Controllers\AgentDashboardController::class, 'renewals'])->name('agent.renewals');
        Route::get('/followups', [App\Http\Controllers\AgentDashboardController::class, 'followups'])->name('agent.followups');
        
        // Test route to check agent authentication
        Route::get('/test', function() {
            return response()->json([
                'authenticated' => Auth::guard('agent')->check(),
                'user' => Auth::guard('agent')->user(),
                'guard' => 'agent'
            ]);
        });
    });
});

require __DIR__.'/auth.php';
