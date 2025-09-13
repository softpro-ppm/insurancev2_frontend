<?php

// Create Agent for Hostinger
// Run this script to create an agent for testing

require_once 'vendor/autoload.php';

use App\Models\Agent;
use Illuminate\Support\Facades\Hash;

echo "🔧 Creating Agent for Hostinger\n";
echo "================================\n\n";

try {
    // Check if agent already exists
    $existingAgent = Agent::where('email', 'chbalaram321@gmail.com')->first();
    
    if ($existingAgent) {
        echo "✅ Agent already exists:\n";
        echo "   Name: " . $existingAgent->name . "\n";
        echo "   Email: " . $existingAgent->email . "\n";
        echo "   Phone: " . $existingAgent->phone . "\n";
        echo "   Status: " . $existingAgent->status . "\n";
        echo "\n";
        
        // Update password to ensure it's correct
        $existingAgent->password = Hash::make('agent123');
        $existingAgent->save();
        echo "✅ Agent password updated to: agent123\n";
        
    } else {
        // Create new agent
        $agent = Agent::create([
            'name' => 'Chinta Balaram Naidu',
            'phone' => '+919876543210',
            'email' => 'chbalaram321@gmail.com',
            'user_id' => 'AG001',
            'status' => 'Active',
            'policies_count' => 0,
            'performance' => 0.00,
            'address' => 'Hyderabad, Telangana',
            'password' => Hash::make('agent123')
        ]);
        
        echo "✅ Agent created successfully:\n";
        echo "   Name: " . $agent->name . "\n";
        echo "   Email: " . $agent->email . "\n";
        echo "   Phone: " . $agent->phone . "\n";
        echo "   Password: agent123\n";
        echo "   Status: " . $agent->status . "\n";
    }
    
    echo "\n";
    echo "🎯 Agent Login Credentials:\n";
    echo "==========================\n";
    echo "Email: chbalaram321@gmail.com\n";
    echo "Password: agent123\n";
    echo "\n";
    echo "🔗 Login URL: https://v2insurance.softpromis.com/agent/login\n";
    echo "🔗 Dashboard URL: https://v2insurance.softpromis.com/agent/dashboard\n";
    
} catch (Exception $e) {
    echo "❌ Error creating agent: " . $e->getMessage() . "\n";
    echo "\n";
    echo "🔧 Manual SQL to create agent:\n";
    echo "=============================\n";
    echo "INSERT INTO agents (name, phone, email, user_id, status, policies_count, performance, address, password, created_at, updated_at) VALUES (\n";
    echo "    'Chinta Balaram Naidu',\n";
    echo "    '+919876543210',\n";
    echo "    'chbalaram321@gmail.com',\n";
    echo "    'AG001',\n";
    echo "    'Active',\n";
    echo "    0,\n";
    echo "    0.00,\n";
    echo "    'Hyderabad, Telangana',\n";
    echo "    '\$2y\$12\$IyxmNN8ICbf3q6NUIvqQgO/wuoTjzpqTeh9r1DjTAuBM6yV0ykRA.',\n";
    echo "    NOW(),\n";
    echo "    NOW()\n";
    echo ");\n";
}

?>
