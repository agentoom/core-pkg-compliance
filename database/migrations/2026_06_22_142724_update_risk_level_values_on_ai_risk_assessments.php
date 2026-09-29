<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure risk_level is a plain string column to support new values
        Schema::table('ai_risk_assessments', function (Blueprint $table) {
            $table->string('risk_level')->default('unknown')->change();
        });

        Schema::table('ai_risk_assessment_versions', function (Blueprint $table) {
            $table->string('risk_level')->nullable()->change();
        });

        // Migrate existing 'high' values to 'potential_high_risk'
        DB::table('ai_risk_assessments')->where('risk_level', 'high')->update(['risk_level' => 'potential_high_risk']);
        DB::table('ai_risk_assessment_versions')->where('risk_level', 'high')->update(['risk_level' => 'potential_high_risk']);
    }

    public function down(): void
    {
        DB::table('ai_risk_assessments')->where('risk_level', 'potential_high_risk')->update(['risk_level' => 'high']);
        DB::table('ai_risk_assessment_versions')->where('risk_level', 'potential_high_risk')->update(['risk_level' => 'high']);
    }
};
