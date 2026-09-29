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
        Schema::create('compliance_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('ai_purpose')->nullable();
            $table->json('ai_actions')->nullable();       // recommend, score, rank, approve_reject
            $table->json('affected_groups')->nullable();  // employees, customers, applicants, etc.
            $table->string('human_review_frequency')->nullable(); // always, usually, sometimes, never
            $table->json('data_processed')->nullable();   // business, customer, employee, financial, health, children
            $table->string('governance_level')->nullable(); // basic, standard, enhanced
            $table->boolean('potentially_sensitive')->default(false);
            $table->json('recommended_controls')->nullable();
            $table->text('reasoning')->nullable();
            $table->foreignId('governance_profile_id')->nullable()->constrained('governance_profiles')->nullOnDelete();
            // Ownership
            $table->nullableMorphs('owner');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_assessments');
    }
};
