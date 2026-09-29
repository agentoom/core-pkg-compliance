<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiPendingDecision;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PendingDecisionIndex extends Component
{
    use WithPagination;

    public function approve(int $id): void
    {
        $decision = AiPendingDecision::findOrFail($id);
        $decision->approve(auth()->user());
        session()->flash('success', 'Decision approved.');
    }

    public function reject(int $id): void
    {
        $decision = AiPendingDecision::findOrFail($id);
        $decision->reject(auth()->user());
        session()->flash('success', 'Decision rejected.');
    }

    public function render(): View
    {
        $decisions = AiPendingDecision::where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('agentoom-compliance::livewire.admin.compliance.pending-decision-index', compact('decisions'))->layout('agentoom-core::layouts.admin');
    }
}
