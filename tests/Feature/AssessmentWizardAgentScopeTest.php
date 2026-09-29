<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\ComplianceAssessment;
use Agentoom\Compliance\Models\GovernanceProfile;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class AssessmentWizardAgentScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
    }

    public function test_wizard_with_agent_id_saves_assessment_linked_to_agent(): void
    {
        $agent = Agent::factory()->create([
            'name' => 'Test Agent',
        ]);

        GovernanceProfile::create([
            'name' => 'Basic Profile',
            'governance_level' => 'basic',
            'active' => true,
        ]);

        $assessment = ComplianceAssessment::create([
            'agent_id' => $agent->id,
            'name' => 'Agent-specific assessment',
            'ai_purpose' => 'customer_support',
            'governance_level' => 'basic',
            'potentially_sensitive' => false,
            'recommended_controls' => ['audit_logs', 'disclosure'],
            'reasoning' => 'Low-risk support agent',
        ]);

        $this->assertDatabaseHas('compliance_assessments', [
            'agent_id' => $agent->id,
            'name' => 'Agent-specific assessment',
        ]);

        $this->assertEquals($agent->id, $assessment->agent->id);
        $this->assertEquals('Test Agent', $assessment->agent->name);
    }

    public function test_wizard_without_agent_id_works_as_instance_level(): void
    {
        GovernanceProfile::create([
            'name' => 'Standard Profile',
            'governance_level' => 'standard',
            'active' => true,
        ]);

        $assessment = ComplianceAssessment::create([
            'agent_id' => null,
            'name' => 'Instance-wide assessment',
            'ai_purpose' => 'workflow_automation',
            'governance_level' => 'standard',
            'potentially_sensitive' => false,
            'recommended_controls' => ['audit_logs', 'evaluations'],
            'reasoning' => 'Medium-risk workflow',
        ]);

        $this->assertNull($assessment->agent_id);
        $this->assertDatabaseHas('compliance_assessments', [
            'agent_id' => null,
            'name' => 'Instance-wide assessment',
        ]);
    }
}
