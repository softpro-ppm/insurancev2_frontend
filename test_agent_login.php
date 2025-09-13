<?php

// Test Agent Login System on Hostinger
// Run this script to check if agent login is working

echo "🔍 Testing Agent Login System on Hostinger\n";
echo "==========================================\n\n";

// Test 1: Check if agent routes are accessible
echo "1. Testing Agent Login Route...\n";
$loginUrl = 'https://v2insurance.softpromis.com/agent/login';
$response = @file_get_contents($loginUrl);

if ($response !== false) {
    echo "✅ Agent login page is accessible\n";
    echo "   URL: $loginUrl\n";
} else {
    echo "❌ Agent login page is NOT accessible\n";
    echo "   URL: $loginUrl\n";
    echo "   Error: " . error_get_last()['message'] . "\n";
}

echo "\n";

// Test 2: Check if agent dashboard is protected
echo "2. Testing Agent Dashboard Protection...\n";
$dashboardUrl = 'https://v2insurance.softpromis.com/agent/dashboard';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: Test Script',
        'timeout' => 10
    ]
]);

$response = @file_get_contents($dashboardUrl, false, $context);

if ($response !== false) {
    if (strpos($response, 'login') !== false || strpos($response, 'redirect') !== false) {
        echo "✅ Agent dashboard is properly protected (redirects to login)\n";
    } else {
        echo "⚠️  Agent dashboard might be accessible without login\n";
    }
} else {
    echo "❌ Agent dashboard is NOT accessible\n";
}

echo "\n";

// Test 3: Check if agent authentication components exist
echo "3. Testing Agent Authentication Components...\n";

$components = [
    'AgentAuthenticatedSessionController' => 'app/Http/Controllers/Auth/AgentAuthenticatedSessionController.php',
    'AgentAuth Middleware' => 'app/Http/Middleware/AgentAuth.php',
    'Agent Login View' => 'resources/views/auth/agent-login.blade.php',
    'Agent Dashboard View' => 'resources/views/agent/dashboard.blade.php',
    'Agent Layout' => 'resources/views/layouts/agent.blade.php',
    'Agent Model' => 'app/Models/Agent.php',
    'Agent Dashboard Controller' => 'app/Http/Controllers/AgentDashboardController.php'
];

foreach ($components as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name exists\n";
    } else {
        echo "❌ $name is MISSING\n";
    }
}

echo "\n";

// Test 4: Check agent database
echo "4. Testing Agent Database...\n";
try {
    // This would need to be run on the server
    echo "ℹ️  To test agent database, run this on Hostinger:\n";
    echo "   php artisan tinker\n";
    echo "   App\\Models\\Agent::count()\n";
    echo "   App\\Models\\Agent::all()\n";
} catch (Exception $e) {
    echo "❌ Database test failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Check configuration
echo "5. Testing Configuration...\n";
$configFiles = [
    'config/auth.php' => 'Auth configuration',
    'bootstrap/app.php' => 'Middleware registration',
    'routes/web.php' => 'Route definitions'
];

foreach ($configFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description exists\n";
    } else {
        echo "❌ $description is MISSING\n";
    }
}

echo "\n";

// Summary
echo "📋 SUMMARY:\n";
echo "===========\n";
echo "To test agent login on Hostinger:\n";
echo "1. Go to: https://v2insurance.softpromis.com/agent/login\n";
echo "2. Try logging in with:\n";
echo "   Email: chbalaram321@gmail.com\n";
echo "   Password: (your agent password)\n";
echo "3. If login works, you should be redirected to agent dashboard\n";
echo "4. If not, check the error messages\n\n";

echo "🔧 If agent login is not working, check:\n";
echo "- Agent exists in database\n";
echo "- Agent password is correct\n";
echo "- All files are deployed to Hostinger\n";
echo "- Routes are properly configured\n";
echo "- Middleware is registered\n";

?>
