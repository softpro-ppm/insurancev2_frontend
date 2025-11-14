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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewals');
    }
};
