<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\GovernanceControl;
use Illuminate\View\View;
use Livewire\Component;

class GovernanceControlIndex extends Component
{
    public string $framework = '';

    public function render(): View
    {
        $controls = GovernanceControl::query()
            ->when($this->framework, fn ($q) => $q->where('framework', $this->framework))
            ->orderBy('framework')
            ->orderBy('code')
            ->get()
            ->groupBy('framework');

        $frameworks = GovernanceControl::distinct()->pluck('framework')->sort()->values();

        return view('agentoom-compliance::livewire.admin.compliance.governance-control-index', compact('controls', 'frameworks'))
            ->layout('agentoom-core::layouts.admin');
    }
}
