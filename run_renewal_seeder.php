<?php
/**
 * Simple script to run the RenewalSeeder on production server
 * This will populate the renewals table with sample data
 */

// Include Laravel bootstrap
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;

try {
    echo "🔄 Running RenewalSeeder on production server...\n";
    
    // Run the seeder
    Artisan::call('db:seed', ['--class' => 'RenewalSeeder']);
    
    echo "✅ RenewalSeeder completed successfully!\n";
    echo "📊 Renewal data has been populated in the database.\n";
    
    // Verify the data
    $renewalCount = \App\Models\Renewal::count();
    echo "📈 Total renewals in database: " . $renewalCount . "\n";
    
    if ($renewalCount > 0) {
        echo "🎉 SUCCESS: Renewal data is now available!\n";
        echo "🔄 Please refresh your dashboard page to see the renewal data.\n";
    } else {
        echo "❌ ERROR: No renewal data found after seeding.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "🔧 Please check your database connection and try again.\n";
}
