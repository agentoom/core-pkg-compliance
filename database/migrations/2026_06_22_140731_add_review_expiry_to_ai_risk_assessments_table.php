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
        Schema::table('ai_risk_assessments', function (Blueprint $table) {
            $table->unsignedInteger('review_interval_days')->nullable()->after('last_reviewed_at');
            $table->timestamp('next_review_due_at')->nullable()->after('review_interval_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_risk_assessments', function (Blueprint $table) {
            $table->dropColumn(['review_interval_days', 'next_review_due_at']);
        });
    }
};
