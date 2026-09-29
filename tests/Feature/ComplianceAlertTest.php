<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiComplianceAlert;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComplianceAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_compliance_alert_can_be_created(): void
    {
        $alert = AiComplianceAlert::create([
            'type' => 'reassessment_required',
            'entity_type' => 'Agent',
            'entity_id' => 1,
            'description' => 'Agent model changed, reassessment required.',
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('ai_compliance_alerts', [
            'type' => 'reassessment_required',
            'status' => 'open',
        ]);

        $this->assertTrue($alert->isOpen());
    }

    public function test_compliance_alert_can_be_resolved(): void
    {
        $alert = AiComplianceAlert::create([
            'type' => 'review_overdue',
            'description' => 'Review is overdue.',
            'status' => 'open',
        ]);

        $alert->resolve();

        $this->assertDatabaseHas('ai_compliance_alerts', [
            'id' => $alert->id,
            'status' => 'resolved',
        ]);

        $this->assertNotNull($alert->fresh()->resolved_at);
        $this->assertFalse($alert->fresh()->isOpen());
    }
}
