<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiPendingDecision;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PendingDecisionOverrideTest extends TestCase
{
    use RefreshDatabase;

    public function test_override_sets_status_custom_decision_and_reason(): void
    {
        $user = User::factory()->create();

        $decision = AiPendingDecision::create([
            'entity_type' => 'pipeline_run',
            'entity_id' => 1,
            'description' => 'Needs review',
            'status' => 'pending',
        ]);

        $decision->override($user, 'proceed_with_conditions', 'Approved with additional safeguards applied.');

        $this->assertDatabaseHas('ai_pending_decisions', [
            'id' => $decision->id,
            'status' => 'overridden',
            'reviewed_by' => $user->id,
            'custom_decision' => 'proceed_with_conditions',
            'override_reason' => 'Approved with additional safeguards applied.',
        ]);

        $fresh = $decision->fresh();
        $this->assertNotNull($fresh->overridden_at);
        $this->assertNotNull($fresh->reviewed_at);
    }
}
