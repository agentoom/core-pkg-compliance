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
        Schema::create('governed_deployments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('business_purpose')->nullable();
            $table->nullableMorphs('deployable'); // Agent, Pipeline, Endpoint
            $table->foreignId('governance_profile_id')->nullable()->constrained('governance_profiles')->nullOnDelete();
            $table->string('classification_governance_level')->nullable();
            $table->boolean('classification_potentially_sensitive')->default(false);
            $table->json('classification_recommended_controls')->nullable();
            $table->text('classification_reasoning')->nullable();
            $table->json('assessment_history')->nullable();
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
        Schema::dropIfExists('governed_deployments');
    }
};
