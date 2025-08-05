<?php

use App\Http\Controllers\ProfileController;
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

// Policies routes
Route::get('/policies', function () {
    return view('policies.index');
})->middleware(['auth', 'verified'])->name('policies.index');

// Renewals routes
Route::get('/renewals', function () {
    return view('renewals.index');
})->middleware(['auth', 'verified'])->name('renewals.index');

// Follow-ups routes
Route::get('/followups', function () {
    return view('followups.index');
})->middleware(['auth', 'verified'])->name('followups.index');

// Reports routes
Route::get('/reports', function () {
    return view('reports.index');
})->middleware(['auth', 'verified'])->name('reports.index');

// Agents routes
Route::get('/agents', function () {
    return view('agents.index');
})->middleware(['auth', 'verified'])->name('agents.index');

// Notifications routes
Route::get('/notifications', function () {
    return view('notifications.index');
})->middleware(['auth', 'verified'])->name('notifications.index');

// Settings routes
Route::get('/settings', function () {
    return view('settings.index');
})->middleware(['auth', 'verified'])->name('settings.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
