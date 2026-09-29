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
        Schema::table('ai_audit_logs', function (Blueprint $table) {
            $table->string('model_provider')->nullable()->after('metadata');
            $table->string('model_name')->nullable()->after('model_provider');
            $table->string('model_version')->nullable()->after('model_name');
            $table->string('workflow_version')->nullable()->after('model_version');
            $table->string('prompt_version')->nullable()->after('workflow_version');
            $table->unsignedInteger('execution_duration_ms')->nullable()->after('prompt_version');
            $table->string('tool_name')->nullable()->after('execution_duration_ms');
            $table->string('tool_arguments_hash')->nullable()->after('tool_name');
            $table->string('tool_result_hash')->nullable()->after('tool_arguments_hash');
            $table->string('trace_id')->nullable()->after('tool_result_hash')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_audit_logs', function (Blueprint $table) {
            $table->dropColumn([
                'model_provider', 'model_name', 'model_version',
                'workflow_version', 'prompt_version', 'execution_duration_ms',
                'tool_name', 'tool_arguments_hash', 'tool_result_hash', 'trace_id',
            ]);
        });
    }
};
