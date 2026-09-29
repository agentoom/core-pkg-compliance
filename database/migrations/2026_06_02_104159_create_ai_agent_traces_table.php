<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_agent_traces', function (Blueprint $table) {
            $table->id();

            // Polymorphic link (allows tracking chats OR standalone jobs)
            $table->nullableMorphs('traceable');

            // Metadata for quick indexing and filtering
            $table->string('conversation_id')->nullable()->index();
            $table->unsignedBigInteger('agent_id')->index(); // ID of your dynamic config
            $table->string('ai_message_id')->nullable()->index(); // Optional link to the precise message turn

            // Metadata & Performance
            $table->string('model')->index();
            $table->unsignedInteger('total_steps')->default(1);

            // Token telemetry
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);

            // Performance metric
            $table->unsignedInteger('execution_time_ms')->nullable();

            // The raw payload (monologue + tool iterations)
            $table->json('steps_json');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_agent_traces');
    }
};
