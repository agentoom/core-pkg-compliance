<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\GovernedDeployment;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class GovernedDeploymentIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $deployments = GovernedDeployment::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->with('governanceProfile')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('agentoom-compliance::livewire.admin.compliance.governed-deployment-index', compact('deployments'))
            ->layout('agentoom-core::layouts.admin');
    }
}
