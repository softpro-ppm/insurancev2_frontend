<?php
/**
 * TEST SAVE NOTE ENDPOINT
 * Quick diagnostic to see if the API is working
 */

// Load Laravel
chdir(__DIR__ . '/..');
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Test Save Note</title>";
echo "<style>
body { font-family: monospace; margin: 40px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
pre { background: #f9fafb; padding: 15px; border-radius: 4px; overflow-x: auto; border: 1px solid #ddd; }
.success { color: #10B981; font-weight: bold; }
.error { color: #EF4444; font-weight: bold; }
.info { color: #3B82F6; font-weight: bold; }
button { padding: 12px 24px; background: #4F46E5; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; }
button:hover { background: #4338CA; }
</style></head><body>";
echo "<div class='container'>";
echo "<h1>🧪 Test Save Note Endpoint</h1>";

echo "<h2>📋 Test Data:</h2>";
echo "<pre>";
$testData = [
    'policyId' => 998,
    'customerName' => 'TEST CUSTOMER',
    'phone' => '9999999999',
    'email' => 'test@example.com',
    'note' => 'This is a test note from diagnostic script',
    'status' => 'Called',
    'nextFollowupDate' => '2025-11-10'
];
print_r($testData);
echo "</pre>";

echo "<h2>🔍 Route Check:</h2>";
echo "<pre>";
$route = \Route::getRoutes()->getByName('followups.save-note');
if ($route) {
    echo "✅ Route exists: " . $route->uri() . "\n";
    echo "✅ Method: POST\n";
    echo "✅ Middleware: " . implode(', ', $route->gatherMiddleware()) . "\n";
} else {
    echo "❌ Route 'followups.save-note' not found!\n";
}
echo "</pre>";

echo "<h2>🧪 Test Save Note:</h2>";
echo "<button onclick='testSaveNote()'>Test Save Note API</button>";
echo "<div id='result' style='margin-top: 20px;'></div>";

echo "<script>
async function testSaveNote() {
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = '<p>🔄 Testing...</p>';
    
    try {
        const response = await fetch('/api/followups/save-note', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                policyId: 998,
                customerName: 'TEST CUSTOMER',
                phone: '9999999999',
                email: 'test@example.com',
                note: 'This is a test note from browser',
                status: 'Called',
                nextFollowupDate: '2025-11-10'
            })
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (response.ok && data.success) {
            resultDiv.innerHTML = '<pre class=\"success\">✅ SUCCESS!\\n\\n' + JSON.stringify(data, null, 2) + '</pre>';
        } else {
            resultDiv.innerHTML = '<pre class=\"error\">❌ FAILED!\\n\\nStatus: ' + response.status + '\\n\\n' + JSON.stringify(data, null, 2) + '</pre>';
        }
    } catch (error) {
        console.error('Error:', error);
        resultDiv.innerHTML = '<pre class=\"error\">❌ ERROR!\\n\\n' + error.message + '</pre>';
    }
}
</script>";

echo "<h2>📊 Check Followups Table:</h2>";
echo "<pre>";
$followupsCount = DB::table('followups')->count();
echo "Total followups in database: <span class='info'>{$followupsCount}</span>\n\n";

if ($followupsCount > 0) {
    $recent = DB::table('followups')->orderByDesc('created_at')->limit(3)->get();
    echo "Recent followups:\n";
    foreach ($recent as $f) {
        echo "• {$f->customer_name} - {$f->status} - {$f->notes}\n";
    }
}
echo "</pre>";

echo "</div></body></html>";

