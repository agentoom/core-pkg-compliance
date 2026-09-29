<?php

namespace Agentoom\Compliance\Services;

use Agentoom\Compliance\Models\GovernanceRecommendation;

/**
 * Classifies AI deployments into governance levels based on business context.
 *
 * This service provides explainable governance recommendations only.
 * It does NOT make legal determinations, risk classifications, or compliance claims.
 */
class ComplianceClassificationService
{
    /**
     * Sensitive use-case triggers that warrant enhanced governance.
     *
     * @var string[]
     */
    private array $enhancedTriggers = [
        'candidate_screening',
        'employee_evaluation',
        'healthcare_support',
        'education_support',
        'finance_decisions',
        'credit_scoring',
    ];

    /**
     * Sensitive data types that increase governance requirements.
     *
     * @var string[]
     */
    private array $sensitiveDataTypes = [
        'health_data',
        'children_data',
        'financial_data',
        'employee_data',
    ];

    /**
     * AI actions that affect people directly and warrant higher oversight.
     *
     * @var string[]
     */
    private array $highImpactActions = [
        'score_people',
        'rank_people',
        'approve_reject_people',
    ];

    /**
     * Classify a deployment based on business context answers.
     *
     * @param  array{
     *     ai_purpose: string,
     *     ai_actions: string[],
     *     affected_groups: string[],
     *     human_review_frequency: string,
     *     data_processed: string[]
     * }  $answers
     * @return array{
     *     governance_level: string,
     *     potentially_sensitive: bool,
     *     recommended_controls: string[],
     *     reasoning: string
     * }
     */
    public function classify(array $answers): array
    {
        $governanceLevel = 'basic';
        $potentiallySensitive = false;
        $reasons = [];
        $controls = ['audit_logs'];

        $purpose = $answers['ai_purpose'] ?? '';
        $actions = $answers['ai_actions'] ?? [];
        $affectedGroups = $answers['affected_groups'] ?? [];
        $humanReview = $answers['human_review_frequency'] ?? 'always';
        $dataProcessed = $answers['data_processed'] ?? [];

        // Check database recommendations first
        $dbRecommendation = GovernanceRecommendation::where('trigger', $purpose)->first();
        if ($dbRecommendation) {
            $governanceLevel = $dbRecommendation->recommendation;
            $reasons[] = $dbRecommendation->rationale ?? "Use case '{$purpose}' matches governance recommendation.";
        }

        // Enhanced triggers
        if (in_array($purpose, $this->enhancedTriggers)) {
            $governanceLevel = 'enhanced';
            $potentiallySensitive = true;
            $reasons[] = "Use case '{$purpose}' involves potentially sensitive decisions about people.";
        }

        // High-impact actions on people
        $matchedActions = array_intersect($actions, $this->highImpactActions);
        if (! empty($matchedActions)) {
            $governanceLevel = $this->escalate($governanceLevel, 'enhanced');
            $potentiallySensitive = true;
            $reasons[] = 'AI can '.implode(', ', $matchedActions).', which directly affects individuals.';
            $controls[] = 'human_oversight';
            $controls[] = 'evaluations';
        }

        // Sensitive data
        $matchedData = array_intersect($dataProcessed, $this->sensitiveDataTypes);
        if (! empty($matchedData)) {
            $governanceLevel = $this->escalate($governanceLevel, 'standard');
            $potentiallySensitive = true;
            $reasons[] = 'Processes sensitive data: '.implode(', ', $matchedData).'.';
            $controls[] = 'disclosure';
            $controls[] = 'periodic_reviews';
        }

        // Human review frequency
        if ($humanReview === 'never') {
            $governanceLevel = $this->escalate($governanceLevel, 'enhanced');
            $reasons[] = 'No human review in place — enhanced monitoring recommended.';
            $controls[] = 'monitoring';
        } elseif ($humanReview === 'sometimes') {
            $governanceLevel = $this->escalate($governanceLevel, 'standard');
            $controls[] = 'human_oversight';
        }

        // Affected groups
        $vulnerableGroups = array_intersect($affectedGroups, ['applicants', 'students', 'patients', 'citizens']);
        if (! empty($vulnerableGroups)) {
            $governanceLevel = $this->escalate($governanceLevel, 'standard');
            $potentiallySensitive = true;
            $reasons[] = 'Affects potentially vulnerable groups: '.implode(', ', $vulnerableGroups).'.';
        }

        // Standard minimum controls
        if ($governanceLevel === 'standard') {
            $controls = array_merge($controls, ['evaluations', 'incidents', 'periodic_reviews']);
        } elseif ($governanceLevel === 'enhanced') {
            $controls = array_merge($controls, ['human_oversight', 'evaluations', 'incidents', 'monitoring', 'periodic_reviews', 'disclosure']);
        }

        return [
            'governance_level' => $governanceLevel,
            'potentially_sensitive' => $potentiallySensitive,
            'recommended_controls' => array_values(array_unique($controls)),
            'reasoning' => implode(' ', $reasons) ?: 'Standard governance profile applies.',
        ];
    }

    /**
     * Escalate governance level only upward (never downgrade).
     */
    private function escalate(string $current, string $proposed): string
    {
        $levels = ['basic' => 1, 'standard' => 2, 'enhanced' => 3];

        return ($levels[$proposed] ?? 1) > ($levels[$current] ?? 1) ? $proposed : $current;
    }
}
