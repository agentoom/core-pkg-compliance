<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiComplianceReport;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ComplianceReportShow extends Component
{
    public int $reportId;

    public function mount(int $reportId): void
    {
        $this->reportId = $reportId;
    }

    public function render(): View
    {
        $report = AiComplianceReport::findOrFail($this->reportId);

        return view('agentoom-compliance::livewire.admin.compliance.compliance-report-show', compact('report'))
            ->layout('agentoom-core::layouts.admin');
    }
}
