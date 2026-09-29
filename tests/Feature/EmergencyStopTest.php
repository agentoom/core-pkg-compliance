<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiEmergencyStop;
use Agentoom\Core\Ai\Agents\DynamicAgent;
use Agentoom\Core\Exceptions\EmergencyStopActiveException;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmergencyStopTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_activate_emergency_stop(): void
    {
        $stop = AiEmergencyStop::activateFor('Agent', 1, 'admin', 'Safety concern');

        $this->assertDatabaseHas('ai_emergency_stops', [
            'entity_type' => 'agent',
            'entity_id' => 1,
            'is_active' => true,
            'activated_by' => 'admin',
            'reason' => 'Safety concern',
        ]);

        $this->assertTrue($stop->is_active);
    }

    public function test_is_stopped_for_returns_true_when_active(): void
    {
        AiEmergencyStop::activateFor('Pipeline', 5, 'admin');

        $this->assertTrue(AiEmergencyStop::isStoppedFor('Pipeline', 5));
    }

    public function test_is_stopped_for_returns_false_when_no_stop(): void
    {
        $this->assertFalse(AiEmergencyStop::isStoppedFor('Agent', 99));
    }

    public function test_can_lift_emergency_stop(): void
    {
        $stop = AiEmergencyStop::activateFor('Endpoint', 3, 'admin');
        $stop->lift('superadmin');

        $this->assertDatabaseHas('ai_emergency_stops', [
            'id' => $stop->id,
            'is_active' => false,
            'reactivated_by' => 'superadmin',
        ]);

        $this->assertFalse(AiEmergencyStop::isStoppedFor('Endpoint', 3));
    }

    public function test_lifted_stop_does_not_affect_new_active_stop(): void
    {
        $stop = AiEmergencyStop::activateFor('Agent', 2, 'admin');
        $stop->lift('superadmin');

        AiEmergencyStop::activateFor('Agent', 2, 'admin', 'New concern');

        $this->assertTrue(AiEmergencyStop::isStoppedFor('Agent', 2));
    }

    public function test_stream_is_blocked_when_emergency_stop_is_active(): void
    {
        config([
            'agentoom-compliance.enabled' => true,
            'agentoom-compliance.features.emergency_stops' => true,
        ]);

        $agent = Agent::factory()->create();
        AiEmergencyStop::activateFor('agent', $agent->id, 'admin', 'Safety concern');

        $dynamicAgent = new DynamicAgent($agent);

        $this->expectException(EmergencyStopActiveException::class);

        // We need to iterate the stream to trigger the check — but the check
        // happens before the stream even starts, so calling stream() is enough.
        $dynamicAgent->stream('Hello');
    }

    public function test_stream_guard_check_does_not_block_when_no_stop_active(): void
    {
        config([
            'agentoom-compliance.enabled' => true,
            'agentoom-compliance.features.emergency_stops' => true,
        ]);

        $agent = Agent::factory()->create();

        // The guard check happens before the stream is created, so we verify
        // no exception is thrown during the guard phase. We use a try/catch
        // to distinguish guard exceptions from downstream AI provider errors.
        $threwEmergencyStop = false;

        try {
            $dynamicAgent = new DynamicAgent($agent);
            $dynamicAgent->stream('Hello');
        } catch (EmergencyStopActiveException) {
            $threwEmergencyStop = true;
        } catch (\Throwable) {
            // Downstream AI provider errors are expected in test environment
            // and do not indicate an emergency stop issue.
        }

        $this->assertFalse($threwEmergencyStop, 'Stream should not be blocked by emergency stop when no stop is active.');
    }

    public function test_is_stopped_for_is_case_insensitive(): void
    {
        config([
            'agentoom-compliance.enabled' => true,
            'agentoom-compliance.features.emergency_stops' => true,
        ]);

        // Activate with TitleCase (simulating the old admin UI behavior)
        AiEmergencyStop::activateFor('Agent', 42, 'admin');

        // isStoppedFor with lowercase should match (the runtime check)
        $this->assertTrue(AiEmergencyStop::isStoppedFor('agent', 42));
    }

    public function test_stream_is_blocked_when_emergency_stop_has_titlecase_entity_type(): void
    {
        config([
            'agentoom-compliance.enabled' => true,
            'agentoom-compliance.features.emergency_stops' => true,
        ]);

        $agent = Agent::factory()->create();
        // Simulate the old UI behavior: entity_type stored as 'Agent' (TitleCase)
        AiEmergencyStop::activateFor('Agent', $agent->id, 'admin', 'Safety concern');

        $dynamicAgent = new DynamicAgent($agent);

        $this->expectException(EmergencyStopActiveException::class);
        $dynamicAgent->stream('Hello');
    }
}
