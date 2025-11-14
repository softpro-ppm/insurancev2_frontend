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
        Schema::table('policies', function (Blueprint $table) {
            // Add policy_issue_date column (when policy was issued by agent)
            $table->date('policy_issue_date')->nullable()->after('insurance_type');
            
            // Add is_renewed flag (Yes/No to track if policy was renewed)
            $table->enum('is_renewed', ['Yes', 'No'])->default('No')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn(['policy_issue_date', 'is_renewed']);
        });
    }
};
