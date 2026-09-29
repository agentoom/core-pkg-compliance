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
        $tables = [
            'ai_risk_assessments',
            'ai_incidents',
            'ai_evaluations',
            'ai_compliance_alerts',
            'ai_compliance_reports',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'owner_type')) {
                    $table->nullableMorphs('owner');
                    $table->timestamp('assigned_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'ai_risk_assessments',
            'ai_incidents',
            'ai_evaluations',
            'ai_compliance_alerts',
            'ai_compliance_reports',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'owner_type')) {
                    $table->dropMorphs('owner');
                    $table->dropColumn('assigned_at');
                }
            });
        }
    }
};
