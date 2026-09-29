<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Core\Ai\Agents\DynamicAgent;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\Configuration;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Ai;

class AgentAuditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_trace_when_agent_is_prompted()
    {
        Configuration::updateOrCreate(['key' => 'app_ai_credits'], ['value' => 1000]);

        $agentModel = Agent::factory()->create();
        $dynamicAgent = new DynamicAgent($agentModel);

        Ai::fakeAgent(DynamicAgent::class, [
            '*' => 'Hello, I am an AI.',
        ]);

        $response = $dynamicAgent->prompt('Hi');

        $this->assertDatabaseHas('ai_agent_traces', [
            'agent_id' => $agentModel->id,
        ]);

        $trace = AiAgentTrace::first();
        $this->assertNotNull($trace->execution_time_ms);
        $this->assertIsArray($trace->steps_json);
    }

    public function test_it_creates_a_trace_when_agent_is_streamed()
    {
        Configuration::updateOrCreate(['key' => 'app_ai_credits'], ['value' => 1000]);

        $agentModel = Agent::factory()->create();
        $dynamicAgent = new DynamicAgent($agentModel);

        Ai::fakeAgent(DynamicAgent::class, [
            '*' => 'Hello, I am a streaming AI.',
        ]);

        $stream = $dynamicAgent->stream('Hi');

        // Trace should not be created yet as we haven't iterated the stream
        $this->assertDatabaseCount('ai_agent_traces', 0);

        // Iterate the stream
        foreach ($stream as $event) {
            // Just consume
        }

        $this->assertDatabaseHas('ai_agent_traces', [
            'agent_id' => $agentModel->id,
        ]);

        $trace = AiAgentTrace::first();
        $this->assertNotNull($trace->execution_time_ms);
    }
}
