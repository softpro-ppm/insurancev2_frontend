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
        Schema::table('policy_versions', function (Blueprint $table) {
            // Add policy_issue_date column for historical records
            $table->date('policy_issue_date')->nullable()->after('policy_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policy_versions', function (Blueprint $table) {
            $table->dropColumn('policy_issue_date');
        });
    }
};
