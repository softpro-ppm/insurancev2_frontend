<?php
// Test what's missing on Hostinger
// Run this on Hostinger to check agent login system

echo "🔍 Checking Agent Login System on Hostinger\n";
echo "==========================================\n\n";

// Check if files exist
$files_to_check = [
    'app/Http/Controllers/Auth/AgentAuthenticatedSessionController.php' => 'Agent Login Controller',
    'app/Http/Controllers/AgentDashboardController.php' => 'Agent Dashboard Controller',
    'app/Http/Middleware/AgentAuth.php' => 'Agent Auth Middleware',
    'app/Models/Agent.php' => 'Agent Model',
    'resources/views/auth/agent-login.blade.php' => 'Agent Login View',
    'resources/views/layouts/agent.blade.php' => 'Agent Layout',
    'resources/views/agent/dashboard.blade.php' => 'Agent Dashboard View',
    'config/auth.php' => 'Auth Configuration',
    'bootstrap/app.php' => 'Bootstrap App',
    'routes/web.php' => 'Web Routes'
];

echo "📁 Checking Files:\n";
foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description exists\n";
    } else {
        echo "❌ $description is MISSING\n";
    }
}

echo "\n";

// Check if agent routes are defined
echo "🛣️ Checking Routes:\n";
try {
    $routes = file_get_contents('routes/web.php');
    if (strpos($routes, 'agent.login') !== false) {
        echo "✅ Agent login route exists\n";
    } else {
        echo "❌ Agent login route is MISSING\n";
    }
    
    if (strpos($routes, 'agent.dashboard') !== false) {
        echo "✅ Agent dashboard route exists\n";
    } else {
        echo "❌ Agent dashboard route is MISSING\n";
    }
    
    if (strpos($routes, 'AgentAuthenticatedSessionController') !== false) {
        echo "✅ Agent controller is referenced in routes\n";
    } else {
        echo "❌ Agent controller is NOT referenced in routes\n";
    }
} catch (Exception $e) {
    echo "❌ Cannot read routes file: " . $e->getMessage() . "\n";
}

echo "\n";

// Check database
echo "🗄️ Checking Database:\n";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=u820431346_v2insurance', 'username', 'password');
    $stmt = $pdo->query("SELECT COUNT(*) FROM agents");
    $count = $stmt->fetchColumn();
    echo "✅ Agents table exists with $count agents\n";
    
    $stmt = $pdo->query("SELECT email FROM agents WHERE email = 'chbalaram321@gmail.com'");
    if ($stmt->fetch()) {
        echo "✅ Agent chbalaram321@gmail.com exists\n";
    } else {
        echo "❌ Agent chbalaram321@gmail.com is MISSING\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "📋 SUMMARY:\n";
echo "===========\n";
echo "If you see ❌ errors above, those files need to be uploaded to Hostinger.\n";
echo "All files are ready in the 'agent_login_fix' folder.\n";
echo "Upload them to fix the 404 error!\n";

?>
