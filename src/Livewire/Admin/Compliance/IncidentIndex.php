<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiIncident;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class IncidentIndex extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $incidents = AiIncident::query()
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('agentoom-compliance::livewire.admin.compliance.incident-index', compact('incidents'))->layout('agentoom-core::layouts.admin');
    }
}
