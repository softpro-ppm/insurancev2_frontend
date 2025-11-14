<?php
/**
 * TEST FOLLOW UPS DASHBOARD
 * Quick diagnostic to see what data is being returned
 */

// Load Laravel
chdir(__DIR__ . '/..');
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Test Follow Ups</title>";
echo "<style>
body { font-family: monospace; margin: 40px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
pre { background: #f9fafb; padding: 15px; border-radius: 4px; overflow-x: auto; border: 1px solid #ddd; }
.success { color: #10B981; font-weight: bold; }
.error { color: #EF4444; font-weight: bold; }
.info { color: #3B82F6; font-weight: bold; }
</style></head><body>";
echo "<div class='container'>";
echo "<h1>🔍 Follow Ups Dashboard Test</h1>";

try {
    $today = now()->startOfDay();
    
    echo "<h2>📅 Date Ranges:</h2>";
    echo "<pre>";
    echo "Today: " . $today->format('Y-m-d') . "\n\n";
    
    $lastMonthStart = now()->copy()->subMonth()->startOfMonth();
    $lastMonthEnd = now()->copy()->subMonth()->endOfMonth();
    echo "Last Month: " . $lastMonthStart->format('Y-m-d') . " to " . $lastMonthEnd->format('Y-m-d') . "\n";
    
    $currentMonthStart = now()->copy()->startOfMonth();
    $currentMonthEnd = now()->copy()->endOfMonth();
    echo "Current Month: " . $currentMonthStart->format('Y-m-d') . " to " . $currentMonthEnd->format('Y-m-d') . "\n";
    
    $nextMonthStart = now()->copy()->addMonth()->startOfMonth();
    $nextMonthEnd = now()->copy()->addMonth()->endOfMonth();
    echo "Next Month: " . $nextMonthStart->format('Y-m-d') . " to " . $nextMonthEnd->format('Y-m-d') . "\n";
    echo "</pre>";
    
    echo "<h2>📊 Database Counts:</h2>";
    $totalPolicies = DB::table('policies')->count();
    $policiesWithEndDate = DB::table('policies')->whereNotNull('end_date')->count();
    
    echo "<pre>";
    echo "Total Policies: <span class='info'>{$totalPolicies}</span>\n";
    echo "Policies with end_date: <span class='info'>{$policiesWithEndDate}</span>\n";
    echo "</pre>";
    
    echo "<h2>🔍 Sample Policies (first 5):</h2>";
    $samplePolicies = DB::table('policies')
        ->whereNotNull('end_date')
        ->limit(5)
        ->get(['id', 'customer_name', 'end_date', 'status']);
    
    echo "<pre>";
    foreach ($samplePolicies as $policy) {
        echo "ID: {$policy->id}, Customer: {$policy->customer_name}, End Date: {$policy->end_date}, Status: {$policy->status}\n";
    }
    echo "</pre>";
    
    echo "<h2>🔍 Policies by Month:</h2>";
    echo "<pre>";
    
    // Last Month
    $lastMonth = DB::table('policies')
        ->whereNotNull('end_date')
        ->whereDate('end_date', '>=', $lastMonthStart)
        ->whereDate('end_date', '<=', $lastMonthEnd)
        ->count();
    echo "Last Month ({$lastMonthStart->format('M Y')}): <span class='info'>{$lastMonth}</span> policies\n";
    
    // Current Month
    $currentMonth = DB::table('policies')
        ->whereNotNull('end_date')
        ->whereDate('end_date', '>=', $currentMonthStart)
        ->whereDate('end_date', '<=', $currentMonthEnd)
        ->count();
    echo "Current Month ({$currentMonthStart->format('M Y')}): <span class='info'>{$currentMonth}</span> policies\n";
    
    // Next Month
    $nextMonth = DB::table('policies')
        ->whereNotNull('end_date')
        ->whereDate('end_date', '>=', $nextMonthStart)
        ->whereDate('end_date', '<=', $nextMonthEnd)
        ->count();
    echo "Next Month ({$nextMonthStart->format('M Y')}): <span class='info'>{$nextMonth}</span> policies\n";
    
    echo "\nTotal: <span class='success'>" . ($lastMonth + $currentMonth + $nextMonth) . "</span> policies\n";
    echo "</pre>";
    
    echo "<h2>📋 Sample Policies by Category:</h2>";
    
    // Current Month Sample
    if ($currentMonth > 0) {
        echo "<h3>Current Month Expiring:</h3>";
        $currentSample = DB::table('policies')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '>=', $currentMonthStart)
            ->whereDate('end_date', '<=', $currentMonthEnd)
            ->limit(5)
            ->get(['id', 'customer_name', 'end_date', 'policy_type', 'premium']);
        
        echo "<pre>";
        foreach ($currentSample as $policy) {
            $endDate = \Carbon\Carbon::parse($policy->end_date);
            $daysLeft = $today->diffInDays($endDate, false);
            echo "• {$policy->customer_name} - {$policy->policy_type} - Expires: {$endDate->format('d M Y')} ({$daysLeft} days)\n";
        }
        echo "</pre>";
    }
    
    // Next Month Sample
    if ($nextMonth > 0) {
        echo "<h3>Next Month Expiring:</h3>";
        $nextSample = DB::table('policies')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '>=', $nextMonthStart)
            ->whereDate('end_date', '<=', $nextMonthEnd)
            ->limit(5)
            ->get(['id', 'customer_name', 'end_date', 'policy_type', 'premium']);
        
        echo "<pre>";
        foreach ($nextSample as $policy) {
            $endDate = \Carbon\Carbon::parse($policy->end_date);
            $daysLeft = $today->diffInDays($endDate, false);
            echo "• {$policy->customer_name} - {$policy->policy_type} - Expires: {$endDate->format('d M Y')} ({$daysLeft} days)\n";
        }
        echo "</pre>";
    }
    
    echo "<div style='margin-top: 30px; padding: 15px; background: #D1FAE5; border-radius: 8px;'>";
    echo "<h2 class='success'>✅ Test Complete!</h2>";
    echo "<p>Now try the Follow Ups page: <a href='/followups'>/followups</a></p>";
    echo "<p>If you see data here but not on the page, it's a frontend JavaScript issue.</p>";
    echo "<p>If you don't see data here, it's a backend date filtering issue.</p>";
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='padding: 15px; background: #FEE2E2; border-radius: 8px;'>";
    echo "<h2 class='error'>❌ Error</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</div></body></html>";

