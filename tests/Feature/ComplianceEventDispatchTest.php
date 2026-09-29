<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Services\ComplianceEventDispatcher;
use Agentoom\Core\Events\AiActionExecuted;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

class ComplianceEventDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
    }

    public function test_event_is_dispatched_when_audit_logging_enabled(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.audit_logging', true);

        Event::fake([AiActionExecuted::class]);

        ComplianceEventDispatcher::dispatchAgentEvent(
            eventType: 'agent.run.completed',
            agentId: 42,
            input: 'Test prompt',
            output: 'Test response',
        );

        Event::assertDispatched(AiActionExecuted::class, function (AiActionExecuted $event) {
            return $event->entityType === 'agent'
                && $event->entityId === 42
                && $event->eventType === 'agent.run.completed'
                && $event->input === 'Test prompt'
                && $event->output === 'Test response';
        });
    }

    public function test_event_is_not_dispatched_when_audit_logging_disabled(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.audit_logging', false);

        Event::fake([AiActionExecuted::class]);

        ComplianceEventDispatcher::dispatchAgentEvent(
            eventType: 'agent.run.completed',
            agentId: 42,
        );

        Event::assertNotDispatched(AiActionExecuted::class);
    }

    public function test_event_is_not_dispatched_when_master_switch_off(): void
    {
        Config::set('agentoom-compliance.enabled', false);
        Config::set('agentoom-compliance.features.audit_logging', true);

        Event::fake([AiActionExecuted::class]);

        ComplianceEventDispatcher::dispatchAgentEvent(
            eventType: 'agent.run.completed',
            agentId: 42,
        );

        Event::assertNotDispatched(AiActionExecuted::class);
    }

    public function test_task_event_includes_metadata(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.audit_logging', true);

        Event::fake([AiActionExecuted::class]);

        ComplianceEventDispatcher::dispatchTaskEvent(
            eventType: 'task.completed',
            taskId: 100,
            agentId: 42,
            input: 'Task input',
            output: 'Task output',
            userId: 5,
        );

        Event::assertDispatched(AiActionExecuted::class, function (AiActionExecuted $event) {
            return $event->entityType === 'task'
                && $event->entityId === 100
                && $event->eventType === 'task.completed'
                && $event->actorId === '5'
                && $event->actorType === 'user'
                && ($event->metadata['agent_id'] ?? null) === 42;
        });
    }

    public function test_endpoint_event_includes_metadata(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.audit_logging', true);

        Event::fake([AiActionExecuted::class]);

        ComplianceEventDispatcher::dispatchEndpointEvent(
            eventType: 'endpoint.processed',
            endpointId: 10,
            agentId: 42,
        );

        Event::assertDispatched(AiActionExecuted::class, function (AiActionExecuted $event) {
            return $event->entityType === 'endpoint'
                && $event->entityId === 10
                && ($event->metadata['agent_id'] ?? null) === 42;
        });
    }
}
