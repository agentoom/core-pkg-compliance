<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Livewire\Admin\AgentTraceIndex;
use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class AgentTraceIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $agent = Agent::factory()->create();
        AiAgentTrace::create([
            'agent_id' => $agent->id,
            'model' => 'test-model',
            'total_steps' => 1,
            'total_tokens' => 100,
            'execution_time_ms' => 500,
            'steps_json' => [['step_number' => 1, 'text' => 'test step']],
        ]);

        Livewire::test(AgentTraceIndex::class)
            ->assertStatus(200)
            ->assertSee('test-model');
    }

    public function test_it_can_clear_filters()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AgentTraceIndex::class)
            ->set('search', 'test-search')
            ->set('agentId', '1')
            ->set('conversationId', 'conv-123')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('agentId', '')
            ->assertSet('conversationId', '');
    }
}
