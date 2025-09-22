<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Policy;
use App\Models\PolicyVersion;
use Illuminate\Support\Facades\DB;

echo "<h2>🧹 Policy Versions Cleanup Script</h2>\n";
echo "<p><strong>This will delete ALL PolicyVersion records for ALL policies</strong></p>\n";
echo "<p>Current policy data in the main Policy table will remain unchanged.</p>\n";
echo "<hr>\n";

try {
    // Get total count before deletion
    $totalVersions = PolicyVersion::count();
    $totalPolicies = Policy::count();
    
    echo "<h3>📊 Current Status:</h3>\n";
    echo "<p>• Total Policies: <strong>{$totalPolicies}</strong></p>\n";
    echo "<p>• Total Policy Versions: <strong>{$totalVersions}</strong></p>\n";
    echo "<hr>\n";
    
    if ($totalVersions === 0) {
        echo "<p style='color: orange;'>⚠️ No policy versions found to delete.</p>\n";
        exit;
    }
    
    echo "<h3>🗑️ Deleting Policy Versions...</h3>\n";
    
    // Start transaction for safety
    DB::beginTransaction();
    
    try {
        // Delete all policy versions
        $deletedCount = PolicyVersion::query()->delete();
        
        // Reset version counters if needed
        // (This ensures future versions start from 1)
        DB::statement("ALTER TABLE policy_versions AUTO_INCREMENT = 1");
        
        DB::commit();
        
        echo "<p style='color: green;'>✅ Successfully deleted <strong>{$deletedCount}</strong> policy version records!</p>\n";
        echo "<p style='color: green;'>✅ All policy data in main Policy table preserved!</p>\n";
        echo "<p style='color: green;'>✅ Version counter reset for future versions!</p>\n";
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "<p style='color: red;'>❌ Error during deletion: " . $e->getMessage() . "</p>\n";
        echo "<p style='color: orange;'>🔄 Transaction rolled back - no changes made.</p>\n";
        exit;
    }
    
    echo "<hr>\n";
    echo "<h3>📊 Final Status:</h3>\n";
    echo "<p>• Total Policies: <strong>{$totalPolicies}</strong> (unchanged)</p>\n";
    echo "<p>• Policy Versions: <strong>0</strong> (all deleted)</p>\n";
    echo "<p>• Future policy edits will start from Version 1</p>\n";
    
    echo "<hr>\n";
    echo "<h3>🎉 Cleanup Complete!</h3>\n";
    echo "<p><strong>All policy history has been cleaned up successfully!</strong></p>\n";
    echo "<p>• All policies now have clean history</p>\n";
    echo "<p>• Current policy data is preserved</p>\n";
    echo "<p>• Future edits will create clean version history</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Script Error: " . $e->getMessage() . "</p>\n";
}

echo "<hr>\n";
echo "<p><strong>⚠️ Important:</strong> Delete this file (cleanup_policy_versions.php) after use for security.</p>\n";
echo "<p><strong>🚀 Next Steps:</strong> Test your policy history - it should now show clean, single versions for all policies.</p>\n";

?>

