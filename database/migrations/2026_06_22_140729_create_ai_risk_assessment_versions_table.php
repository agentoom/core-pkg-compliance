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
        Schema::create('ai_risk_assessment_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('risk_assessment_id');
            $table->unsignedInteger('version_number');
            $table->string('risk_level');
            $table->text('use_case_description')->nullable();
            $table->text('affected_persons')->nullable();
            $table->text('mitigations')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('risk_assessment_id')->references('id')->on('ai_risk_assessments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_risk_assessment_versions');
    }
};
