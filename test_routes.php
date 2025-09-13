<?php
// Simple route test for Hostinger
// Upload this to Hostinger root and run it

echo "🛣️ ROUTE TEST FOR HOSTINGER\n";
echo "===========================\n\n";

// Test if we can access Laravel routes
echo "Testing Laravel Routes:\n";

// Test main route
$main_url = 'https://v2insurance.softpromis.com/';
echo "1. Main site: $main_url\n";
$response = @file_get_contents($main_url);
if ($response !== false) {
    echo "   ✅ Main site accessible\n";
} else {
    echo "   ❌ Main site NOT accessible\n";
}

// Test admin login
$admin_url = 'https://v2insurance.softpromis.com/login';
echo "2. Admin login: $admin_url\n";
$response = @file_get_contents($admin_url);
if ($response !== false) {
    echo "   ✅ Admin login accessible\n";
} else {
    echo "   ❌ Admin login NOT accessible\n";
}

// Test agent login
$agent_url = 'https://v2insurance.softpromis.com/agent/login';
echo "3. Agent login: $agent_url\n";
$response = @file_get_contents($agent_url);
if ($response !== false) {
    echo "   ✅ Agent login accessible\n";
} else {
    echo "   ❌ Agent login NOT accessible (404 error)\n";
}

// Test agent dashboard
$dashboard_url = 'https://v2insurance.softpromis.com/agent/dashboard';
echo "4. Agent dashboard: $dashboard_url\n";
$response = @file_get_contents($dashboard_url);
if ($response !== false) {
    echo "   ✅ Agent dashboard accessible\n";
} else {
    echo "   ❌ Agent dashboard NOT accessible (404 error)\n";
}

echo "\n";

// Check if files exist locally
echo "📁 Local Files Check:\n";
$files_to_check = [
    'app/Http/Controllers/Auth/AgentAuthenticatedSessionController.php',
    'app/Http/Controllers/AgentDashboardController.php',
    'app/Http/Middleware/AgentAuth.php',
    'app/Models/Agent.php',
    'resources/views/auth/agent-login.blade.php',
    'resources/views/layouts/agent.blade.php',
    'resources/views/agent/dashboard.blade.php',
    'config/auth.php',
    'bootstrap/app.php',
    'routes/web.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file is MISSING\n";
    }
}

echo "\n";
echo "🔧 DIAGNOSIS:\n";
echo "=============\n";
echo "If agent login shows 404 but admin login works:\n";
echo "1. Agent routes are missing from routes/web.php\n";
echo "2. Agent controllers are missing\n";
echo "3. Agent views are missing\n";
echo "4. Auto-deployment is not working\n\n";
echo "SOLUTION: Manually upload the missing files!\n";

?>
