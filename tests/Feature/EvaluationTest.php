<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiEvaluation;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_evaluation_can_be_created(): void
    {
        $evaluation = AiEvaluation::create([
            'entity_type' => 'Agent',
            'entity_id' => 1,
            'metric' => 'accuracy',
            'score' => 87.5,
            'dataset_name' => 'test-dataset-v1',
            'evaluated_by' => 'admin',
        ]);

        $this->assertDatabaseHas('ai_evaluations', [
            'entity_type' => 'Agent',
            'entity_id' => 1,
            'metric' => 'accuracy',
        ]);

        $this->assertEquals(87.5, $evaluation->score);
    }

    public function test_evaluation_score_can_be_null(): void
    {
        $evaluation = AiEvaluation::create([
            'entity_type' => 'Pipeline',
            'entity_id' => 2,
            'metric' => 'manual_review',
            'notes' => 'Qualitative assessment only',
        ]);

        $this->assertNull($evaluation->score);
        $this->assertDatabaseHas('ai_evaluations', ['metric' => 'manual_review']);
    }
}
