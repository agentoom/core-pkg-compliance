<?php

namespace Agentoom\Compliance\Services;

use Agentoom\Compliance\Helpers\ComplianceConfig;
use Agentoom\Compliance\Models\AiComplianceAlert;
use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Compliance\Models\AiRiskAssessment;
use Carbon\Carbon;

class ComplianceAlertGenerator
{
    /**
     * Generate compliance alerts for all conditions.
     *
     * Called daily by the compliance:generate-alerts scheduler.
     * No-ops when auto_alerts feature is disabled or master switch is off.
     *
     * @return int Number of alerts generated
     */
    public function generate(): int
    {
        if (! ComplianceConfig::isFeatureEnabled('auto_alerts')) {
            return 0;
        }

        $count = 0;

        $count += $this->generateOverdueReviewAlerts();
        $count += $this->generateHighRiskWithoutMitigationsAlerts();
        $count += $this->generateIncidentRateAlerts();

        return $count;
    }

    /**
     * Generate alerts for risk assessments with overdue reviews.
     */
    protected function generateOverdueReviewAlerts(): int
    {
        $count = 0;

        $overdue = AiRiskAssessment::whereNotNull('next_review_due_at')
            ->where('next_review_due_at', '<', Carbon::now()->startOfDay())
            ->get();

        foreach ($overdue as $assessment) {
            // Don't duplicate — check if an open alert already exists
            $exists = AiComplianceAlert::where('entity_type', 'risk_assessment')
                ->where('entity_id', $assessment->id)
                ->where('type', 'overdue_review')
                ->where('status', 'open')
                ->exists();

            if ($exists) {
                continue;
            }

            AiComplianceAlert::create([
                'type' => 'overdue_review',
                'entity_type' => 'risk_assessment',
                'entity_id' => $assessment->id,
                'description' => sprintf(
                    'Risk assessment #%d (level: %s) is overdue for review since %s.',
                    $assessment->id,
                    $assessment->risk_level,
                    $assessment->next_review_due_at?->toDateString() ?? 'unknown'
                ),
                'status' => 'open',
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Generate alerts for high-risk agents/assessments without documented mitigations.
     */
    protected function generateHighRiskWithoutMitigationsAlerts(): int
    {
        $count = 0;

        $highRisk = AiRiskAssessment::where('risk_level', 'potential_high_risk')
            ->where(function ($query) {
                $query->whereNull('mitigations')
                    ->orWhere('mitigations', '');
            })
            ->get();

        foreach ($highRisk as $assessment) {
            $exists = AiComplianceAlert::where('entity_type', 'risk_assessment')
                ->where('entity_id', $assessment->id)
                ->where('type', 'high_risk_no_mitigations')
                ->where('status', 'open')
                ->exists();

            if ($exists) {
                continue;
            }

            AiComplianceAlert::create([
                'type' => 'high_risk_no_mitigations',
                'entity_type' => 'risk_assessment',
                'entity_id' => $assessment->id,
                'description' => sprintf(
                    'Risk assessment #%d is classified as "potential_high_risk" but has no mitigations documented. Assessable: %s #%d.',
                    $assessment->id,
                    $assessment->assessable_type ?? 'unknown',
                    $assessment->assessable_id ?? 0
                ),
                'status' => 'open',
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Generate alerts if the incident rate exceeds thresholds.
     *
     * Fires when there are more than 5 open high/critical incidents from the
     * last 7 days that haven't been acknowledged.
     */
    protected function generateIncidentRateAlerts(): int
    {
        $count = 0;

        $incidentCount = AiIncident::whereIn('severity', ['high', 'critical'])
            ->where('status', 'open')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        if ($incidentCount < 5) {
            return 0;
        }

        $exists = AiComplianceAlert::where('type', 'high_incident_rate')
            ->where('status', 'open')
            ->where('created_at', '>=', Carbon::now()->subDay()->startOfDay())
            ->exists();

        if ($exists) {
            return 0; // Already alerted today
        }

        AiComplianceAlert::create([
            'type' => 'high_incident_rate',
            'description' => sprintf(
                '%d high/critical incidents are open in the last 7 days. Consider reviewing the incident dashboard.',
                $incidentCount
            ),
            'status' => 'open',
        ]);

        return 1;
    }
}
