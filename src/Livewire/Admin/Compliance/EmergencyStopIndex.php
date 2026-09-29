<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiEmergencyStop;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\Endpoint;
use Agentoom\Core\Models\Pipeline;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class EmergencyStopIndex extends Component
{
    use WithPagination;

    public string $entityType = 'agent';

    public string $entityId = '';

    public function updatedEntityType(): void
    {
        $this->entityId = '';
    }

    public function getEntityOptionsProperty(): EloquentCollection|Collection
    {
        return match ($this->entityType) {
            'agent' => Agent::orderBy('name')->get(['id', 'name']),
            'pipeline' => Pipeline::orderBy('name')->get(['id', 'name']),
            'endpoint' => Endpoint::orderBy('name')->get(['id', 'name']),
            default => collect(),
        };
    }

    public string $reason = '';

    public function activate(): void
    {
        $this->validate([
            'entityType' => 'required|string',
            'entityId' => 'required',
            'reason' => 'nullable|string|max:500',
        ]);

        AiEmergencyStop::activateFor(
            $this->entityType,
            $this->entityId,
            auth()->user()->name ?? auth()->id(),
            $this->reason ?: null
        );

        $this->reset(['entityId', 'reason']);
        session()->flash('success', 'Emergency stop activated.');
    }

    public function lift(int $stopId): void
    {
        $stop = AiEmergencyStop::findOrFail($stopId);
        $stop->lift(auth()->user()->name ?? auth()->id());
        session()->flash('success', 'Emergency stop lifted.');
    }

    public function render(): View
    {
        $stops = AiEmergencyStop::latest()->paginate(15);

        return view('agentoom-compliance::livewire.admin.compliance.emergency-stop-index', [
            'stops' => $stops,
            'entityOptions' => $this->entityOptions,
        ])
            ->layout('agentoom-core::layouts.admin');
    }
}
