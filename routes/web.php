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

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard API routes
Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
Route::get('/api/dashboard/recent-policies', [DashboardController::class, 'getRecentPolicies'])->name('dashboard.recent-policies');
Route::get('/api/dashboard/expiring-policies', [DashboardController::class, 'getExpiringPolicies'])->name('dashboard.expiring-policies');

// Policies routes
Route::get('/policies', function () {
    return view('policies.index');
})->middleware(['auth', 'verified'])->name('policies.index');

// Policy CRUD routes
Route::get('/api/policies', [PolicyController::class, 'index'])->name('policies.list');
Route::post('/policies', [PolicyController::class, 'store'])->name('policies.store');
Route::get('/policies/{id}', [PolicyController::class, 'show'])->name('policies.show');
Route::put('/policies/{id}', [PolicyController::class, 'update'])->name('policies.update');
Route::delete('/policies/{id}', [PolicyController::class, 'destroy'])->name('policies.destroy');

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
