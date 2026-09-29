<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiIncident;
use Illuminate\View\View;
use Livewire\Component;

class IncidentCreateEdit extends Component
{
    public ?int $incidentId = null;

    public string $severity = 'high';

    public string $description = '';

    public string $status = 'open';

    public ?string $resolved_at = null;

    public ?string $notified_authority_at = null;

    public function mount(?int $incidentId = null): void
    {
        $this->incidentId = $incidentId;

        if ($incidentId) {
            $incident = AiIncident::findOrFail($incidentId);
            $this->severity = $incident->severity;
            $this->description = $incident->description;
            $this->status = $incident->status;
            $this->resolved_at = $incident->resolved_at?->format('Y-m-d\TH:i');
            $this->notified_authority_at = $incident->notified_authority_at?->format('Y-m-d\TH:i');
        }
    }

    public function save(): void
    {
        $this->validate([
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string',
            'status' => 'required|in:open,investigating,resolved',
            'resolved_at' => 'nullable|date',
            'notified_authority_at' => 'nullable|date',
        ]);

        $data = [
            'severity' => $this->severity,
            'description' => $this->description,
            'status' => $this->status,
            'resolved_at' => $this->resolved_at ?: null,
            'notified_authority_at' => $this->notified_authority_at ?: null,
        ];

        if ($this->incidentId) {
            AiIncident::findOrFail($this->incidentId)->update($data);
            session()->flash('success', 'Incident updated.');
        } else {
            $data['reported_by'] = auth()->id() ?? 'system';
            AiIncident::create($data);
            session()->flash('success', 'Incident created.');
        }

        $this->redirect(route('admin.compliance.incidents.index'), navigate: true);
    }

    public function render(): View
    {
        return view('agentoom-compliance::livewire.admin.compliance.incident-create-edit')->layout('agentoom-core::layouts.admin');
    }
}
