<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiAuditLog;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_can_be_created(): void
    {
        $log = AiAuditLog::create([
            'entity_type' => 'Agent',
            'entity_id' => 1,
            'event_type' => 'agent_run',
            'actor_type' => 'user',
            'actor_id' => '1',
            'input_hash' => hash('sha256', 'input'),
            'output_hash' => hash('sha256', 'output'),
        ]);

        $this->assertDatabaseHas('ai_audit_logs', ['entity_type' => 'Agent', 'entity_id' => 1]);
        $this->assertNotNull($log->created_at);
    }

    public function test_audit_log_cannot_be_updated(): void
    {
        $log = AiAuditLog::create([
            'entity_type' => 'Agent',
            'entity_id' => 1,
            'event_type' => 'agent_run',
        ]);

        $this->expectException(LogicException::class);

        $log->event_type = 'tool_call';
        $log->save();
    }
}
