<?php
/**
 * SIMPLE BACKUP FILES - Emergency restore package
 */

echo "Creating simple backup files...\n";

// Create minimal working DashboardController
$dashboardController = '<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        try {
            $totalPolicies = Policy::count();
            $activePolicies = Policy::where("status", "Active")->count();
            $expiredPolicies = Policy::where("status", "Expired")->count();
            
            $payload = [
                "stats" => [
                    "totalPolicies" => $totalPolicies,
                    "activePolicies" => $activePolicies,
                    "expiredPolicies" => $expiredPolicies,
                    "totalPremium" => Policy::sum("premium"),
                    "totalRevenue" => Policy::sum("revenue"),
                ]
            ];
            
            return response()->json($payload);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function getRecentPolicies()
    {
        try {
            $recentPolicies = Policy::latest()->limit(10)->get();
            return response()->json(["recentPolicies" => $recentPolicies]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function getExpiringPolicies()
    {
        try {
            $expiringPolicies = Policy::where("end_date", "<=", Carbon::now()->addDays(30))->get();
            return response()->json(["expiringPolicies" => $expiringPolicies]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
}';

file_put_contents('MINIMAL_DashboardController.php', $dashboardController);

// Create minimal working PolicyController
$policyController = '<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;

class PolicyController extends Controller
{
    public function index()
    {
        try {
            $policies = Policy::all();
            return response()->json(["policies" => $policies]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $policy = Policy::create($request->all());
            return response()->json(["message" => "Policy created successfully", "policy" => $policy]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            return response()->json(["policy" => $policy]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->update($request->all());
            return response()->json(["message" => "Policy updated successfully", "policy" => $policy]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->delete();
            return response()->json(["message" => "Policy deleted successfully"]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
}';

file_put_contents('MINIMAL_PolicyController.php', $policyController);

// Create working .env
$envContent = 'APP_NAME="Insurance MS 2.0"
APP_ENV=production
APP_KEY=base64:ZcluVpE3zyA3myeyjGI7Il2ne22PwkITV0Y7mX+YmNI=
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

BROADCAST_DRIVER=log
CACHE_STORE=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"';

file_put_contents('MINIMAL_ENV.txt', $envContent);

echo "✅ MINIMAL BACKUP FILES CREATED!\n";
echo "Files created:\n";
echo "1. MINIMAL_DashboardController.php\n";
echo "2. MINIMAL_PolicyController.php\n";
echo "3. MINIMAL_ENV.txt\n\n";
echo "🚨 EMERGENCY RESTORE STEPS:\n";
echo "1. Upload MINIMAL_DashboardController.php → app/Http/Controllers/DashboardController.php\n";
echo "2. Upload MINIMAL_PolicyController.php → app/Http/Controllers/PolicyController.php\n";
echo "3. Replace .env with MINIMAL_ENV.txt content\n";
echo "4. Run: php artisan cache:clear\n";
echo "5. Test your site!\n\n";
echo "This minimal version should work immediately! 🚀\n";
?>
