<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Compliance\Models\AiRiskAssessmentVersion;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RiskAssessmentVersioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_risk_assessment_creates_version(): void
    {
        $agent = Agent::factory()->create();

        $assessment = AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'minimal',
            'use_case_description' => 'Original description',
            'mitigations' => 'Original mitigations',
            'reviewed_by' => 'admin',
        ]);

        $assessment->update([
            'risk_level' => 'high',
            'use_case_description' => 'Updated description',
        ]);

        $this->assertDatabaseHas('ai_risk_assessment_versions', [
            'risk_assessment_id' => $assessment->id,
            'version_number' => 1,
            'risk_level' => 'minimal',
            'use_case_description' => 'Original description',
        ]);
    }

    public function test_multiple_updates_create_sequential_versions(): void
    {
        $agent = Agent::factory()->create();

        $assessment = AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'minimal',
        ]);

        $assessment->update(['risk_level' => 'limited']);
        $assessment->update(['risk_level' => 'high']);

        $this->assertEquals(2, AiRiskAssessmentVersion::where('risk_assessment_id', $assessment->id)->count());
    }

    public function test_versions_are_immutable_snapshots(): void
    {
        $agent = Agent::factory()->create();

        $assessment = AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'minimal',
            'mitigations' => 'Original mitigations',
        ]);

        $assessment->update(['risk_level' => 'high', 'mitigations' => 'New mitigations']);

        $version = AiRiskAssessmentVersion::where('risk_assessment_id', $assessment->id)->first();
        $this->assertEquals('minimal', $version->risk_level);
        $this->assertEquals('Original mitigations', $version->mitigations);
    }

    public function test_review_expiry_sets_next_review_due_at(): void
    {
        $agent = Agent::factory()->create();

        $assessment = AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'high',
        ]);

        $assessment->update([
            'review_interval_days' => 90,
            'last_reviewed_at' => now(),
        ]);

        $assessment->refresh();
        $this->assertNotNull($assessment->next_review_due_at);
        $this->assertTrue($assessment->next_review_due_at->isFuture());
    }

    public function test_is_review_overdue_returns_true_when_past_due(): void
    {
        $agent = Agent::factory()->create();

        $assessment = AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'high',
            'next_review_due_at' => now()->subDay(),
        ]);

        $this->assertTrue($assessment->isReviewOverdue());
    }

    public function test_is_review_overdue_returns_false_when_not_due(): void
    {
        $agent = Agent::factory()->create();

        $assessment = AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'minimal',
            'next_review_due_at' => now()->addDays(30),
        ]);

        $this->assertFalse($assessment->isReviewOverdue());
    }
}
