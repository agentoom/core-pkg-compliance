<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RiskAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_risk_assessment_can_be_created_and_retrieved_via_morph(): void
    {
        $agent = Agent::factory()->create();

        $assessment = AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'high',
            'use_case_description' => 'Customer support automation',
            'affected_persons' => 'End users',
            'mitigations' => 'Human review for edge cases',
            'last_reviewed_at' => now(),
            'reviewed_by' => 'admin',
        ]);

        $this->assertDatabaseHas('ai_risk_assessments', [
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'high',
        ]);

        $retrieved = AiRiskAssessment::find($assessment->id);
        $this->assertInstanceOf(Agent::class, $retrieved->assessable);
        $this->assertEquals($agent->id, $retrieved->assessable->id);
    }
}
