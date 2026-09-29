<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiPendingDecision;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PendingDecisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_approve_sets_status_and_reviewed_by(): void
    {
        $user = User::factory()->create();

        $decision = AiPendingDecision::create([
            'entity_type' => 'pipeline_run',
            'entity_id' => 1,
            'description' => 'Needs review',
            'status' => 'pending',
        ]);

        $decision->approve($user);

        $this->assertDatabaseHas('ai_pending_decisions', [
            'id' => $decision->id,
            'status' => 'approved',
            'reviewed_by' => $user->id,
        ]);

        $this->assertNotNull($decision->fresh()->reviewed_at);
    }

    public function test_reject_sets_status_and_reviewed_by(): void
    {
        $user = User::factory()->create();

        $decision = AiPendingDecision::create([
            'entity_type' => 'pipeline_run',
            'entity_id' => 1,
            'status' => 'pending',
        ]);

        $decision->reject($user);

        $this->assertDatabaseHas('ai_pending_decisions', [
            'id' => $decision->id,
            'status' => 'rejected',
            'reviewed_by' => $user->id,
        ]);
    }
}
