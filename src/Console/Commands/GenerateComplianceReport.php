<?php

namespace Agentoom\Compliance\Console\Commands;

use Agentoom\Compliance\Models\AiAuditLog;
use Agentoom\Compliance\Models\AiComplianceReport;
use Agentoom\Compliance\Models\AiEvaluation;
use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Compliance\Models\AiPendingDecision;
use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Core\Models\Configuration;
use Illuminate\Console\Command;

class GenerateComplianceReport extends Command
{
    protected $signature = 'ai:generate-compliance-report {--frequency= : weekly, monthly, or quarterly}';

    protected $description = 'Generate a scheduled EU AI Act compliance summary report';

    public function handle(): int
    {
        $frequency = $this->option('frequency')
            ?? Configuration::getValue('compliance_report_frequency')
            ?? 'monthly';

        [$from, $to] = match ($frequency) {
            'weekly' => [now()->subWeek(), now()],
            'quarterly' => [now()->subMonths(3), now()],
            default => [now()->subMonth(), now()],
        };

        $incidentCount = AiIncident::whereBetween('created_at', [$from, $to])->count();
        $incidentsBySeverity = AiIncident::whereBetween('created_at', [$from, $to])
            ->selectRaw('severity, count(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity')
            ->toArray();

        $overdueReviews = AiRiskAssessment::whereNotNull('next_review_due_at')
            ->where('next_review_due_at', '<', now())
            ->count();

        $auditLogCount = AiAuditLog::whereBetween('created_at', [$from, $to])->count();

        $decisions = AiPendingDecision::whereBetween('reviewed_at', [$from, $to])
            ->whereIn('status', ['approved', 'rejected', 'overridden'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $averageEvaluationScore = AiEvaluation::whereNotNull('score')
            ->whereBetween('created_at', [$from, $to])
            ->avg('score');

        $summary = [
            'period_start' => $from->toDateString(),
            'period_end' => $to->toDateString(),
            'incidents' => [
                'total' => $incidentCount,
                'by_severity' => $incidentsBySeverity,
            ],
            'risk_assessments' => [
                'overdue_reviews' => $overdueReviews,
            ],
            'audit_activity' => [
                'log_entries' => $auditLogCount,
            ],
            'human_interventions' => $decisions,
            'evaluation_scores' => [
                'average' => $averageEvaluationScore !== null ? round((float) $averageEvaluationScore, 2) : null,
            ],
        ];

        $title = ucfirst($frequency).' Compliance Report — '.$from->format('Y-m-d').' to '.$to->format('Y-m-d');

        AiComplianceReport::create([
            'title' => $title,
            'frequency' => $frequency,
            'period_start' => $from->toDateString(),
            'period_end' => $to->toDateString(),
            'summary' => $summary,
            'generated_by' => 'system',
        ]);

        $this->info("Compliance report generated: {$title}");

        return self::SUCCESS;
    }
}
