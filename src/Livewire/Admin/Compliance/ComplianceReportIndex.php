<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Console\Commands\GenerateComplianceReport;
use Agentoom\Compliance\Models\AiComplianceReport;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\WithPagination;

class ComplianceReportIndex extends Component
{
    use WithPagination;

    public string $frequency = 'monthly';

    public function generate(): void
    {
        Artisan::call(GenerateComplianceReport::class, ['--frequency' => $this->frequency]);
        session()->flash('success', 'Compliance report generated.');
    }

    public function render(): View
    {
        $reports = AiComplianceReport::latest()->paginate(15);

        return view('agentoom-compliance::livewire.admin.compliance.compliance-report-index', compact('reports'))
            ->layout('agentoom-core::layouts.admin');
    }
}
