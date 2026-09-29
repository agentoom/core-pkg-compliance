<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiComplianceAlert;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ComplianceAlertIndex extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function resolve(int $alertId): void
    {
        $alert = AiComplianceAlert::findOrFail($alertId);
        $alert->resolve();
        session()->flash('success', 'Alert resolved.');
    }

    public function render(): View
    {
        $alerts = AiComplianceAlert::query()
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('agentoom-compliance::livewire.admin.compliance.compliance-alert-index', compact('alerts'))
            ->layout('agentoom-core::layouts.admin');
    }
}
