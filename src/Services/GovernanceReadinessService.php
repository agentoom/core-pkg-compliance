<?php

namespace Agentoom\Compliance\Services;

use Agentoom\Compliance\Models\AiComplianceAlert;
use Agentoom\Compliance\Models\AiEmergencyStop;
use Agentoom\Compliance\Models\AiEvaluation;
use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Compliance\Models\GovernedDeployment;

/**
 * Calculates a governance readiness score (0–100) for internal use.
 *
 * This score is an internal readiness indicator only.
 * It does NOT represent legal compliance or certification status.
 */
class GovernanceReadinessService
{
    /**
     * Calculate the overall governance readiness score.
     *
     * @return array{score: int, breakdown: array<string, array{score: int, max: int, label: string}>}
     */
    public function calculate(): array
    {
        $breakdown = [
            'assessments' => $this->scoreAssessments(),
            'ownership' => $this->scoreOwnership(),
            'evaluations' => $this->scoreEvaluations(),
            'monitoring' => $this->scoreMonitoring(),
            'incidents' => $this->scoreIncidents(),
            'reviews' => $this->scoreReviews(),
        ];

        $totalScore = array_sum(array_column($breakdown, 'score'));
        $totalMax = array_sum(array_column($breakdown, 'max'));

        $score = $totalMax > 0 ? (int) round(($totalScore / $totalMax) * 100) : 0;

        return [
            'score' => $score,
            'breakdown' => $breakdown,
        ];
    }

    /** @return array{score: int, max: int, label: string} */
    private function scoreAssessments(): array
    {
        $deployments = GovernedDeployment::count();
        $withAssessments = GovernedDeployment::whereNotNull('classification_governance_level')->count();

        $score = $deployments > 0 ? (int) round(($withAssessments / $deployments) * 20) : 0;

        return ['score' => $score, 'max' => 20, 'label' => 'Governance assessments completed'];
    }

    /** @return array{score: int, max: int, label: string} */
    private function scoreOwnership(): array
    {
        $total = AiRiskAssessment::count() + AiIncident::count() + AiEvaluation::count();
        $owned = AiRiskAssessment::whereNotNull('owner_id')->count()
            + AiIncident::whereNotNull('owner_id')->count()
            + AiEvaluation::whereNotNull('owner_id')->count();

        $score = $total > 0 ? (int) round(($owned / $total) * 20) : 0;

        return ['score' => $score, 'max' => 20, 'label' => 'Ownership assigned to governance objects'];
    }

    /** @return array{score: int, max: int, label: string} */
    private function scoreEvaluations(): array
    {
        $hasEvaluations = AiEvaluation::count() > 0;
        $avgScore = AiEvaluation::avg('score') ?? 0;

        $score = 0;
        if ($hasEvaluations) {
            $score += 10;
        }
        if ($avgScore >= 70) {
            $score += 10;
        } elseif ($avgScore >= 40) {
            $score += 5;
        }

        return ['score' => $score, 'max' => 20, 'label' => 'Evaluations performed'];
    }

    /** @return array{score: int, max: int, label: string} */
    private function scoreMonitoring(): array
    {
        $activeStops = AiEmergencyStop::where('is_active', true)->count();
        $openAlerts = AiComplianceAlert::where('status', 'open')->count();

        $score = 20;
        if ($activeStops > 0) {
            $score -= min(10, $activeStops * 5);
        }
        if ($openAlerts > 5) {
            $score -= min(10, ($openAlerts - 5) * 2);
        }

        return ['score' => max(0, $score), 'max' => 20, 'label' => 'Monitoring and alerts'];
    }

    /** @return array{score: int, max: int, label: string} */
    private function scoreIncidents(): array
    {
        $total = AiIncident::count();
        $resolved = AiIncident::where('status', 'resolved')->count();

        $score = $total === 0 ? 10 : (int) round(($resolved / $total) * 10);

        return ['score' => $score, 'max' => 10, 'label' => 'Incidents resolved'];
    }

    /** @return array{score: int, max: int, label: string} */
    private function scoreReviews(): array
    {
        $total = AiRiskAssessment::count();
        $overdue = AiRiskAssessment::whereNotNull('next_review_due_at')
            ->where('next_review_due_at', '<', now())
            ->count();

        $upToDate = $total - $overdue;
        $score = $total > 0 ? (int) round(($upToDate / $total) * 10) : 0;

        return ['score' => $score, 'max' => 10, 'label' => 'Risk reviews up to date'];
    }
}
