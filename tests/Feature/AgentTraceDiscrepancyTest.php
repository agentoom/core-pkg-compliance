<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Livewire\Admin\AgentTraceIndex;
use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\AgentConversation;
use Agentoom\Core\Models\AgentConversationMessage;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;

class AgentTraceDiscrepancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reproduces_the_count_discrepancy()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $agent = Agent::factory()->create(['name' => 'Test Agent']);
        $convId = 'conv-discrepancy';

        AgentConversation::create(['id' => $convId, 'agent_id' => $agent->id, 'title' => 'Discrepancy Conv']);

        // Create 6 messages (3 user, 3 assistant)
        for ($i = 0; $i < 3; $i++) {
            // User message
            AgentConversationMessage::create([
                'id' => Str::uuid(),
                'conversation_id' => $convId,
                'agent' => 'agent',
                'role' => 'user',
                'content' => 'user message '.$i,
                'attachments' => [], 'tool_calls' => [], 'tool_results' => [], 'usage' => [], 'meta' => [],
            ]);

            // Assistant message
            $assistantMsgId = Str::uuid();
            AgentConversationMessage::create([
                'id' => $assistantMsgId,
                'conversation_id' => $convId,
                'agent' => 'agent',
                'role' => 'assistant',
                'content' => 'assistant message '.$i,
                'attachments' => [], 'tool_calls' => [], 'tool_results' => [], 'usage' => [], 'meta' => [],
            ]);

            // Create 3 traces (only for assistant messages)
            AiAgentTrace::create([
                'conversation_id' => $convId,
                'agent_id' => $agent->id,
                'ai_message_id' => $assistantMsgId,
                'model' => 'test-model',
                'total_steps' => 1,
                'total_tokens' => 100,
                'steps_json' => [],
            ]);
        }

        // Verify that in the list view, the count is 3 (traces)
        Livewire::test(AgentTraceIndex::class)
            ->assertStatus(200)
            ->assertViewHas('conversations', function ($conversations) {
                return $conversations->first()->conversation->traces_count === 3;
            });

        // Verify that in the detail view, only 3 traces are shown
        Livewire::test(AgentTraceIndex::class)
            ->call('selectConversation', $convId)
            ->assertViewHas('traces', function ($traces) {
                return $traces->count() === 3;
            });
    }
}
