<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cleanup orphaned records that would prevent foreign key creation
        DB::table('agent_conversations')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('agents')
                    ->whereColumn('agents.id', 'agent_conversations.agent_id');
            })
            ->whereNotNull('agent_id')
            ->delete();

        DB::table('agent_conversation_messages')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('agent_conversations')
                    ->whereColumn('agent_conversations.id', 'agent_conversation_messages.conversation_id');
            })
            ->delete();

        DB::table('ai_agent_traces')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('agent_conversations')
                    ->whereColumn('agent_conversations.id', 'ai_agent_traces.conversation_id');
            })
            ->whereNotNull('conversation_id')
            ->delete();

        DB::table('ai_agent_traces')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('agents')
                    ->whereColumn('agents.id', 'ai_agent_traces.agent_id');
            })
            ->delete();

        DB::table('ai_agent_traces')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('agent_conversation_messages')
                    ->whereColumn('agent_conversation_messages.id', 'ai_agent_traces.ai_message_id');
            })
            ->whereNotNull('ai_message_id')
            ->delete();

        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')
                ->references('id')
                ->on('agents')
                ->cascadeOnDelete();
        });

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->foreign('conversation_id')
                ->references('id')
                ->on('agent_conversations')
                ->cascadeOnDelete();
        });

        Schema::table('ai_agent_traces', function (Blueprint $table) {
            // Adjust lengths to match referenced columns (string 36 for UUIDs)
            $table->string('conversation_id', 36)->nullable()->change();
            $table->string('ai_message_id', 36)->nullable()->change();

            $table->foreign('conversation_id')
                ->references('id')
                ->on('agent_conversations')
                ->cascadeOnDelete();

            $table->foreign('agent_id')
                ->references('id')
                ->on('agents')
                ->cascadeOnDelete();

            $table->foreign('ai_message_id')
                ->references('id')
                ->on('agent_conversation_messages')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_agent_traces', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['agent_id']);
            $table->dropForeign(['ai_message_id']);

            $table->string('conversation_id')->nullable()->change();
            $table->string('ai_message_id')->nullable()->change();
        });

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
        });

        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')
                ->references('id')
                ->on('agents')
                ->nullOnDelete();
        });
    }
};
