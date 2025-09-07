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
        Schema::create('policy_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->integer('version_number');
            $table->string('policy_type');
            $table->string('business_type');
            $table->string('customer_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('company_name');
            $table->string('insurance_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('premium', 10, 2);
            $table->decimal('payout', 10, 2);
            $table->decimal('customer_paid_amount', 10, 2);
            $table->decimal('revenue', 10, 2);
            $table->string('status')->default('Active');
            
            // Document paths for this version
            $table->string('policy_copy_path')->nullable();
            $table->string('rc_copy_path')->nullable();
            $table->string('aadhar_copy_path')->nullable();
            $table->string('pan_copy_path')->nullable();
            
            // Metadata
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable(); // Who created this version
            $table->timestamp('version_created_at'); // When this version was active
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('policy_id')->references('id')->on('policies')->onDelete('cascade');
            
            // Indexes
            $table->index(['policy_id', 'version_number']);
            $table->index('version_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_versions');
    }
};