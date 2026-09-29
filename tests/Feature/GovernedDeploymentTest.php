<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\GovernanceProfile;
use Agentoom\Compliance\Models\GovernedDeployment;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GovernedDeploymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_governed_deployment_can_be_created(): void
    {
        $deployment = GovernedDeployment::create([
            'name' => 'Customer Support Agent',
            'business_purpose' => 'Handles customer queries via chat.',
        ]);

        $this->assertDatabaseHas('governed_deployments', [
            'name' => 'Customer Support Agent',
        ]);

        $this->assertNull($deployment->classification_governance_level);
    }

    public function test_apply_classification_stores_result_and_history(): void
    {
        $deployment = GovernedDeployment::create(['name' => 'Test Deployment']);

        $deployment->applyClassification([
            'governance_level' => 'enhanced',
            'potentially_sensitive' => true,
            'recommended_controls' => ['audit_logs', 'human_oversight'],
            'reasoning' => 'Candidate screening use case.',
        ]);

        $deployment->refresh();

        $this->assertEquals('enhanced', $deployment->classification_governance_level);
        $this->assertTrue($deployment->classification_potentially_sensitive);
        $this->assertContains('audit_logs', $deployment->classification_recommended_controls);
        $this->assertCount(1, $deployment->assessment_history);
        $this->assertArrayHasKey('assessed_at', $deployment->assessment_history[0]);
    }

    public function test_multiple_classifications_accumulate_history(): void
    {
        $deployment = GovernedDeployment::create(['name' => 'Test Deployment']);

        $deployment->applyClassification(['governance_level' => 'basic', 'potentially_sensitive' => false, 'recommended_controls' => [], 'reasoning' => 'First.']);
        $deployment->refresh();
        $deployment->applyClassification(['governance_level' => 'standard', 'potentially_sensitive' => false, 'recommended_controls' => [], 'reasoning' => 'Second.']);
        $deployment->refresh();

        $this->assertCount(2, $deployment->assessment_history);
        $this->assertEquals('standard', $deployment->classification_governance_level);
    }

    public function test_governance_profile_relationship(): void
    {
        $profile = GovernanceProfile::create([
            'name' => 'Enhanced Governance',
            'governance_level' => 'enhanced',
            'controls' => ['audit_logs'],
            'active' => true,
        ]);

        $deployment = GovernedDeployment::create([
            'name' => 'Test',
            'governance_profile_id' => $profile->id,
        ]);

        $this->assertEquals($profile->id, $deployment->governanceProfile->id);
    }

    public function test_ownership_can_be_assigned(): void
    {
        $user = User::factory()->create();
        $deployment = GovernedDeployment::create(['name' => 'Test']);

        $deployment->assignOwner($user);

        $this->assertEquals($user->id, $deployment->fresh()->owner_id);
        $this->assertNotNull($deployment->fresh()->assigned_at);
    }
}
