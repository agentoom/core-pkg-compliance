<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiEmergencyStop;
use Agentoom\Compliance\Services\ComplianceGuard;
use Agentoom\Core\Exceptions\EmergencyStopActiveException;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;

class ComplianceGuardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
    }

    public function test_emergency_stop_blocks_execution(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.emergency_stops', true);

        AiEmergencyStop::create([
            'entity_type' => 'agent',
            'entity_id' => 42,
            'reason' => 'Security investigation',
            'activated_by' => 'admin',
        ]);

        $this->expectException(EmergencyStopActiveException::class);
        ComplianceGuard::assertNotStopped('agent', 42);
    }

    public function test_lifted_stop_allows_execution(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.emergency_stops', true);

        $stop = AiEmergencyStop::create([
            'entity_type' => 'agent',
            'entity_id' => 42,
            'reason' => 'Test stop',
            'activated_by' => 'admin',
            'is_active' => true,
        ]);
        $stop->lift('admin');

        ComplianceGuard::assertNotStopped('agent', 42);
        $this->assertTrue(true);
    }

    public function test_master_switch_disables_checks(): void
    {
        Config::set('agentoom-compliance.enabled', false);
        Config::set('agentoom-compliance.features.emergency_stops', true);

        AiEmergencyStop::create([
            'entity_type' => 'agent',
            'entity_id' => 42,
            'reason' => 'Should be ignored',
            'activated_by' => 'admin',
        ]);

        ComplianceGuard::assertNotStopped('agent', 42);
        $this->assertTrue(true);
    }

    public function test_different_entity_types_are_independent(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.emergency_stops', true);

        AiEmergencyStop::create([
            'entity_type' => 'agent',
            'entity_id' => 42,
            'reason' => 'Agent stopped',
            'activated_by' => 'admin',
        ]);

        ComplianceGuard::assertNotStopped('pipeline', 10);
        ComplianceGuard::assertNotStopped('task', 100);

        $this->expectException(EmergencyStopActiveException::class);
        ComplianceGuard::assertNotStopped('agent', 42);
    }
}
