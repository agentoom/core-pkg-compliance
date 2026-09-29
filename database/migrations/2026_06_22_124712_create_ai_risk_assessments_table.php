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
        Schema::create('ai_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->morphs('assessable');
            $table->enum('risk_level', ['minimal', 'limited', 'high'])->default('minimal');
            $table->text('use_case_description')->nullable();
            $table->text('affected_persons')->nullable();
            $table->text('mitigations')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_risk_assessments');
    }
};
