<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing tables
        Schema::dropIfExists('renewals');
        Schema::dropIfExists('policies');
        
        // Recreate policies table without policy_number
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('policy_type');
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('company_name');
            $table->string('insurance_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('premium', 10, 2);
            $table->decimal('payout', 10, 2)->default(0.00);
            $table->decimal('customer_paid_amount', 10, 2);
            $table->decimal('revenue', 10, 2);
            $table->string('status')->default('Active');
            $table->string('business_type');
            $table->string('agent_name');
            // Health/Life specific fields
            $table->unsignedSmallInteger('customer_age')->nullable();
            $table->string('customer_gender')->nullable();
            $table->decimal('sum_insured', 12, 2)->nullable(); // Health
            $table->decimal('sum_assured', 12, 2)->nullable(); // Life
            $table->string('policy_term')->nullable(); // Life
            $table->string('premium_frequency')->nullable(); // Life
            $table->text('policy_copy_path')->nullable();
            $table->text('rc_copy_path')->nullable();
            $table->text('aadhar_copy_path')->nullable();
            $table->text('pan_copy_path')->nullable();
            $table->timestamps();
        });
        
        // Recreate renewals table without policy_number
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('policy_type');
            $table->decimal('current_premium', 10, 2);
            $table->decimal('renewal_premium', 10, 2);
            $table->date('due_date');
            $table->string('status')->default('Pending');
            $table->string('agent_name');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables
        Schema::dropIfExists('renewals');
        Schema::dropIfExists('policies');
        
        // Recreate with policy_number columns
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number')->unique();
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('policy_type');
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('company_name');
            $table->string('insurance_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('premium', 10, 2);
            $table->decimal('payout', 10, 2)->default(0.00);
            $table->decimal('customer_paid_amount', 10, 2);
            $table->decimal('revenue', 10, 2);
            $table->string('status')->default('Active');
            $table->string('business_type');
            $table->string('agent_name');
            $table->text('policy_copy_path')->nullable();
            $table->text('rc_copy_path')->nullable();
            $table->text('aadhar_copy_path')->nullable();
            $table->text('pan_copy_path')->nullable();
            $table->timestamps();
        });
        
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number');
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('policy_type');
            $table->decimal('current_premium', 10, 2);
            $table->decimal('renewal_premium', 10, 2);
            $table->date('due_date');
            $table->string('status')->default('Pending');
            $table->string('agent_name');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
