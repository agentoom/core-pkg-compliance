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
        Schema::create('ai_emergency_stops', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->boolean('is_active')->default(true);
            $table->string('activated_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->string('reactivated_by')->nullable();
            $table->timestamp('reactivated_at')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_emergency_stops');
    }
};
