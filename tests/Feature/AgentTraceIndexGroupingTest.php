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

class AgentTraceIndexGroupingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_groups_traces_by_conversation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $agent = Agent::factory()->create(['name' => 'Test Agent']);

        $conv1 = 'conv-1';
        $conv2 = 'conv-2';

        // Create conversation records first
        AgentConversation::create(['id' => $conv1, 'agent_id' => $agent->id, 'title' => 'Conv 1']);
        AgentConversation::create(['id' => $conv2, 'agent_id' => $agent->id, 'title' => 'Conv 2']);

        // Create traces for conv 1
        AiAgentTrace::create([
            'conversation_id' => $conv1,
            'agent_id' => $agent->id,
            'model' => 'model-a',
            'total_steps' => 1,
            'total_tokens' => 100,
            'steps_json' => [],
        ]);

        AiAgentTrace::create([
            'conversation_id' => $conv1,
            'agent_id' => $agent->id,
            'model' => 'model-a',
            'total_steps' => 2,
            'total_tokens' => 200,
            'steps_json' => [],
        ]);

        // Create trace for conv 2
        AiAgentTrace::create([
            'conversation_id' => $conv2,
            'agent_id' => $agent->id,
            'model' => 'model-b',
            'total_steps' => 3,
            'total_tokens' => 300,
            'steps_json' => [],
        ]);

        AgentConversationMessage::create([
            'id' => Str::uuid(),
            'conversation_id' => $conv1,
            'agent' => 'agent',
            'role' => 'user',
            'content' => 'hello',
            'attachments' => [],
            'tool_calls' => [],
            'tool_results' => [],
            'usage' => [],
            'meta' => [],
        ]);

        Livewire::test(AgentTraceIndex::class)
            ->assertStatus(200)
            ->assertViewHas('conversations', function ($conversations) {
                return $conversations->count() === 2;
            })
            ->assertSee('model-a')
            ->assertSee('model-b')
            ->call('selectConversation', $conv1)
            ->assertSet('selectedConversationId', $conv1)
            ->assertViewHas('traces', function ($traces) use ($conv1) {
                return $traces->count() === 2 && $traces->every(fn ($t) => $t->conversation_id === $conv1);
            })
            ->assertSee('Back to Conversations');
    }
}
