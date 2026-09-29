<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiComplianceAlert;
use Agentoom\Compliance\Models\AiEmergencyStop;
use Agentoom\Compliance\Models\AiEvaluation;
use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Compliance\Models\AiPendingDecision;
use Agentoom\Compliance\Models\AiRiskAssessment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ComplianceDashboard extends Component
{
    public string $period = '30';

    public ?string $customFrom = null;

    public ?string $customTo = null;

    public function render(): View
    {
        $from = match ($this->period) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            'custom' => $this->customFrom ? Carbon::parse($this->customFrom) : now()->subDays(30),
            default => now()->subDays(30),
        };

        $to = $this->period === 'custom' && $this->customTo
            ? Carbon::parse($this->customTo)
            : now();

        $incidentsBySeverity = AiIncident::whereBetween('created_at', [$from, $to])
            ->selectRaw('severity, count(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity');

        $incidentsOverTime = AiIncident::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, count(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $decisions = AiPendingDecision::whereBetween('reviewed_at', [$from, $to])
            ->whereIn('status', ['approved', 'rejected', 'overridden'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalDecisions = $decisions->sum();
        $approvalRate = $totalDecisions > 0 ? round(($decisions->get('approved', 0) / $totalDecisions) * 100, 1) : null;
        $rejectionRate = $totalDecisions > 0 ? round(($decisions->get('rejected', 0) / $totalDecisions) * 100, 1) : null;
        $overrideRate = $totalDecisions > 0 ? round(($decisions->get('overridden', 0) / $totalDecisions) * 100, 1) : null;

        $overdueReviews = AiRiskAssessment::whereNotNull('next_review_due_at')
            ->where('next_review_due_at', '<', now())
            ->count();

        $openAlerts = AiComplianceAlert::where('status', 'open')->count();

        $activeEmergencyStops = AiEmergencyStop::where('is_active', true)->count();

        $emergencyStopEvents = AiEmergencyStop::whereBetween('created_at', [$from, $to])->count();

        $averageEvaluationScore = AiEvaluation::whereNotNull('score')
            ->whereBetween('created_at', [$from, $to])
            ->avg('score');

        return view('agentoom-compliance::livewire.admin.compliance.compliance-dashboard', compact(
            'incidentsBySeverity',
            'incidentsOverTime',
            'approvalRate',
            'rejectionRate',
            'overrideRate',
            'overdueReviews',
            'openAlerts',
            'activeEmergencyStops',
            'emergencyStopEvents',
            'averageEvaluationScore',
        ))->layout('agentoom-core::layouts.admin');
    }
}
