<?php
// Debug script to check what's on Hostinger
// Upload this file to Hostinger root and run it

echo "🔍 DEBUGGING AGENT LOGIN 404 ERROR\n";
echo "==================================\n\n";

// Check if we're on Hostinger
echo "🌐 Server Info:\n";
echo "Server: " . $_SERVER['SERVER_NAME'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Path: " . __FILE__ . "\n\n";

// Check if Laravel is working
echo "🔧 Laravel Check:\n";
if (file_exists('artisan')) {
    echo "✅ Laravel artisan file exists\n";
} else {
    echo "❌ Laravel artisan file NOT found\n";
}

if (file_exists('public/index.php')) {
    echo "✅ Laravel public/index.php exists\n";
} else {
    echo "❌ Laravel public/index.php NOT found\n";
}

echo "\n";

// Check agent files
echo "📁 Agent Files Check:\n";
$agent_files = [
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

foreach ($agent_files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description exists\n";
    } else {
        echo "❌ $description is MISSING\n";
    }
}

echo "\n";

// Check routes
echo "🛣️ Routes Check:\n";
if (file_exists('routes/web.php')) {
    $routes_content = file_get_contents('routes/web.php');
    if (strpos($routes_content, 'agent.login') !== false) {
        echo "✅ Agent login route found in web.php\n";
    } else {
        echo "❌ Agent login route NOT found in web.php\n";
    }
    
    if (strpos($routes_content, 'AgentAuthenticatedSessionController') !== false) {
        echo "✅ Agent controller referenced in routes\n";
    } else {
        echo "❌ Agent controller NOT referenced in routes\n";
    }
} else {
    echo "❌ routes/web.php NOT found\n";
}

echo "\n";

// Check .htaccess
echo "📄 .htaccess Check:\n";
if (file_exists('public/.htaccess')) {
    echo "✅ public/.htaccess exists\n";
    $htaccess_content = file_get_contents('public/.htaccess');
    if (strpos($htaccess_content, 'RewriteEngine') !== false) {
        echo "✅ RewriteEngine found in .htaccess\n";
    } else {
        echo "❌ RewriteEngine NOT found in .htaccess\n";
    }
} else {
    echo "❌ public/.htaccess NOT found\n";
}

echo "\n";

// Check database connection
echo "🗄️ Database Check:\n";
try {
    // Try to connect to database
    $pdo = new PDO('mysql:host=localhost;dbname=u820431346_v2insurance', 'u820431346_v2insurance', 'password');
    echo "✅ Database connection successful\n";
    
    // Check agents table
    $stmt = $pdo->query("SHOW TABLES LIKE 'agents'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Agents table exists\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM agents");
        $count = $stmt->fetchColumn();
        echo "✅ Agents table has $count records\n";
    } else {
        echo "❌ Agents table does NOT exist\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "📋 SUMMARY:\n";
echo "===========\n";
echo "If you see ❌ errors above, those are the issues causing the 404 error.\n";
echo "Upload the missing files to fix the problem.\n";

?>
