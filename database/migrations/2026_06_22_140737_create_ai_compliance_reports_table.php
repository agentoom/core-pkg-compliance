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
        Schema::create('ai_compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('frequency');
            $table->date('period_start');
            $table->date('period_end');
            $table->json('summary');
            $table->string('generated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_compliance_reports');
    }
};
