<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiEvaluation;
use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Compliance\Models\GovernedDeployment;
use Agentoom\Compliance\Services\GovernanceReadinessService;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GovernanceReadinessServiceTest extends TestCase
{
    use RefreshDatabase;

    private GovernanceReadinessService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GovernanceReadinessService;
    }

    public function test_returns_score_and_breakdown(): void
    {
        $result = $this->service->calculate();

        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('breakdown', $result);
        $this->assertIsInt($result['score']);
        $this->assertGreaterThanOrEqual(0, $result['score']);
        $this->assertLessThanOrEqual(100, $result['score']);
    }

    public function test_breakdown_contains_all_dimensions(): void
    {
        $result = $this->service->calculate();

        $this->assertArrayHasKey('assessments', $result['breakdown']);
        $this->assertArrayHasKey('ownership', $result['breakdown']);
        $this->assertArrayHasKey('evaluations', $result['breakdown']);
        $this->assertArrayHasKey('monitoring', $result['breakdown']);
        $this->assertArrayHasKey('incidents', $result['breakdown']);
        $this->assertArrayHasKey('reviews', $result['breakdown']);
    }

    public function test_score_increases_with_evaluations(): void
    {
        $baseResult = $this->service->calculate();

        AiEvaluation::create([
            'entity_type' => 'Agent',
            'entity_id' => 1,
            'metric' => 'accuracy',
            'score' => 85,
        ]);

        $newResult = $this->service->calculate();

        $this->assertGreaterThan($baseResult['score'], $newResult['score']);
    }

    public function test_score_increases_with_resolved_incidents(): void
    {
        AiIncident::create([
            'severity' => 'low',
            'description' => 'Test incident',
            'status' => 'open',
        ]);

        $openResult = $this->service->calculate();

        AiIncident::query()->update(['status' => 'resolved']);

        $resolvedResult = $this->service->calculate();

        $this->assertGreaterThanOrEqual($openResult['score'], $resolvedResult['score']);
    }

    public function test_score_increases_with_owned_objects(): void
    {
        $agent = Agent::factory()->create();

        AiRiskAssessment::create([
            'assessable_type' => Agent::class,
            'assessable_id' => $agent->id,
            'risk_level' => 'minimal',
        ]);

        $noOwnerResult = $this->service->calculate();

        AiRiskAssessment::query()->update([
            'owner_type' => User::class,
            'owner_id' => 1,
        ]);

        $withOwnerResult = $this->service->calculate();

        $this->assertGreaterThanOrEqual($noOwnerResult['score'], $withOwnerResult['score']);
    }

    public function test_governed_deployment_with_classification_improves_score(): void
    {
        GovernedDeployment::create(['name' => 'Unclassified']);
        $unclassifiedResult = $this->service->calculate();

        GovernedDeployment::query()->update(['classification_governance_level' => 'basic']);
        $classifiedResult = $this->service->calculate();

        $this->assertGreaterThanOrEqual($unclassifiedResult['score'], $classifiedResult['score']);
    }
}
