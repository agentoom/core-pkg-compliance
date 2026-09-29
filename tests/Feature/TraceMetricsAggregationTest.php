<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Services\MetricsAggregationService;
use Agentoom\Core\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TraceMetricsAggregationTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_aggregation_consumes_agent_traces(): void
    {
        $hour = Carbon::now()->subHour()->startOfHour();

        $agent = Agent::factory()->create();

        AiAgentTrace::create([
            'agent_id' => $agent->id,
            'model' => 'gpt-4o',
            'total_tokens' => 500,
            'execution_time_ms' => 1200,
            'total_steps' => 3,
            'steps_json' => [],
            'created_at' => $hour->copy()->addMinutes(10),
            'updated_at' => $hour->copy()->addMinutes(10),
        ]);

        AiAgentTrace::create([
            'agent_id' => $agent->id,
            'model' => 'gpt-4o',
            'total_tokens' => 300,
            'execution_time_ms' => 800,
            'total_steps' => 2,
            'steps_json' => [],
            'created_at' => $hour->copy()->addMinutes(20),
            'updated_at' => $hour->copy()->addMinutes(20),
        ]);

        AiAgentTrace::create([
            'agent_id' => $agent->id,
            'model' => 'claude-sonnet-4',
            'total_tokens' => 1000,
            'execution_time_ms' => 2000,
            'total_steps' => 4,
            'steps_json' => [],
            'created_at' => $hour->copy()->addMinutes(30),
            'updated_at' => $hour->copy()->addMinutes(30),
        ]);

        $snapshot = app(MetricsAggregationService::class)->aggregateHour($hour);

        $this->assertEquals(3, $snapshot->successful_executions);
        $this->assertEquals(3, $snapshot->total_executions);
        $this->assertEquals(1800, $snapshot->total_tokens);
        $this->assertGreaterThan(0, $snapshot->avg_response_time_ms);

        $providerMetrics = $snapshot->provider_metrics;
        $this->assertIsArray($providerMetrics);
        $this->assertArrayHasKey('gpt-4o', $providerMetrics);
        $this->assertArrayHasKey('claude-sonnet-4', $providerMetrics);
        $this->assertEquals(2, $providerMetrics['gpt-4o']['count']);
    }
}
