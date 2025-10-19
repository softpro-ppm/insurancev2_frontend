<?php

// Simple test to check if files are accessible on live server
// Access via: yourwebsite.com/test_server_files.php

?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Files Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Server Files Test</h1>
        
        <h2>Cleanup Scripts Status</h2>
        
        <?php
        $files = [
            'safe_cleanup_versions.php' => 'Safe cleanup script (recommended)',
            'cleanup_versions_web.php' => 'Original cleanup script',
            'cleanup_policy_versions_live.php' => 'Command line cleanup script'
        ];
        
        foreach ($files as $file => $description) {
            if (file_exists(__DIR__ . '/' . $file)) {
                echo "<p class='success'>✅ <strong>{$file}</strong> - {$description}</p>";
                echo "<p><a href='{$file}'>Click here to access {$file}</a></p>";
            } else {
                echo "<p class='error'>❌ <strong>{$file}</strong> - File not found</p>";
            }
        }
        ?>
        
        <hr>
        <h2>Quick Actions</h2>
        <p><strong>If cleanup scripts are available:</strong></p>
        <ul>
            <li>Use <strong>safe_cleanup_versions.php</strong> - It's the safest option</li>
            <li>It will preserve all your 532 customer policy documents</li>
            <li>It will only delete old policy version records</li>
        </ul>
        
        <p><strong>After cleanup:</strong></p>
        <ul>
            <li>Hard refresh your browser (Ctrl+F5 or Cmd+Shift+R)</li>
            <li>Test policy history - should show only Version 1</li>
            <li>Documents should appear in both modals</li>
        </ul>
    </div>
</body>
</html>
