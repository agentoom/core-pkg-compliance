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
        Schema::create('ai_incidents', function (Blueprint $table) {
            $table->id();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('high');
            $table->text('description');
            $table->string('affected_entity_type')->nullable();
            $table->unsignedBigInteger('affected_entity_id')->nullable();
            $table->string('reported_by')->default('system');
            $table->enum('status', ['open', 'investigating', 'resolved'])->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('notified_authority_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_incidents');
    }
};
