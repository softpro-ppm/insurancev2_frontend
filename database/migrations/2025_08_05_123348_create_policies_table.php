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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number')->nullable()->unique();
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
