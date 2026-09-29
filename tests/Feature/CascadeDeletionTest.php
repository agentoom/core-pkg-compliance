<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\AgentConversation;
use Agentoom\Core\Models\AgentConversationMessage;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class CascadeDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversation_deletion_cascades_to_messages_and_traces()
    {
        $agent = Agent::factory()->create();
        $convId = (string) Str::uuid();

        AgentConversation::create([
            'id' => $convId,
            'agent_id' => $agent->id,
            'title' => 'Test Conv',
        ]);

        $message = AgentConversationMessage::create([
            'id' => (string) Str::uuid(),
            'conversation_id' => $convId,
            'agent' => 'agent',
            'role' => 'user',
            'content' => 'Hello',
            'attachments' => [], 'tool_calls' => [], 'tool_results' => [], 'usage' => [], 'meta' => [],
        ]);

        AiAgentTrace::create([
            'conversation_id' => $convId,
            'agent_id' => $agent->id,
            'ai_message_id' => $message->id,
            'model' => 'test-model',
            'steps_json' => [],
        ]);

        $this->assertDatabaseCount('agent_conversations', 1);
        $this->assertDatabaseCount('agent_conversation_messages', 1);
        $this->assertDatabaseCount('ai_agent_traces', 1);

        // Delete conversation
        AgentConversation::find($convId)->delete();

        $this->assertDatabaseCount('agent_conversations', 0);
        $this->assertDatabaseCount('agent_conversation_messages', 0);
        $this->assertDatabaseCount('ai_agent_traces', 0);
    }

    public function test_agent_deletion_behavior_for_traces()
    {
        $agent = Agent::factory()->create();
        $convId = (string) Str::uuid();

        AgentConversation::create([
            'id' => $convId,
            'agent_id' => $agent->id,
            'title' => 'Test Conv',
        ]);

        AiAgentTrace::create([
            'conversation_id' => $convId,
            'agent_id' => $agent->id,
            'model' => 'test-model',
            'steps_json' => [],
        ]);

        $this->assertDatabaseCount('agents', 1);
        $this->assertDatabaseCount('ai_agent_traces', 1);

        // Delete agent
        $agent->delete();

        $this->assertDatabaseCount('agents', 0);

        // We expect conversations to be deleted too (cascade)
        $this->assertDatabaseCount('agent_conversations', 0);

        // Traces should be deleted.
        $this->assertDatabaseCount('ai_agent_traces', 0);
    }
}
