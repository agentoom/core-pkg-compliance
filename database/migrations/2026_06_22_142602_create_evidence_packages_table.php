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
        Schema::create('evidence_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->nullableMorphs('subject'); // Agent, Pipeline, Endpoint
            $table->string('format'); // json, zip
            $table->string('status')->default('pending'); // pending, generating, ready, failed
            $table->string('storage_path')->nullable();
            $table->json('included_sections')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('generated_at')->nullable();
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
        Schema::dropIfExists('evidence_packages');
    }
};
