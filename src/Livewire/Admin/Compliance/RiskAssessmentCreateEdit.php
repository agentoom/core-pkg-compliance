<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\Endpoint;
use Agentoom\Core\Models\Pipeline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class RiskAssessmentCreateEdit extends Component
{
    public ?int $assessmentId = null;

    public string $assessable_type = Agent::class;

    public ?int $assessable_id = null;

    public string $risk_level = 'minimal';

    public string $use_case_description = '';

    public string $affected_persons = '';

    public string $mitigations = '';

    public ?string $last_reviewed_at = null;

    public string $reviewed_by = '';

    /** @var array<string, string> */
    public array $assessableTypes = [
        'Agentoom\\Core\\Models\\Agent' => 'Agent',
        'Agentoom\\Core\\Models\\Pipeline' => 'Pipeline',
        'Agentoom\\Core\\Models\\Endpoint' => 'Endpoint',
    ];

    public function mount(?int $assessmentId = null): void
    {
        $this->assessmentId = $assessmentId;

        if ($assessmentId) {
            $assessment = AiRiskAssessment::findOrFail($assessmentId);
            $this->assessable_type = $assessment->assessable_type;
            $this->assessable_id = $assessment->assessable_id;
            $this->risk_level = $assessment->risk_level;
            $this->use_case_description = $assessment->use_case_description ?? '';
            $this->affected_persons = $assessment->affected_persons ?? '';
            $this->mitigations = $assessment->mitigations ?? '';
            $this->last_reviewed_at = $assessment->last_reviewed_at?->format('Y-m-d');
            $this->reviewed_by = $assessment->reviewed_by ?? '';
        }
    }

    public function getAssessableOptionsProperty(): Collection
    {
        return match ($this->assessable_type) {
            Agent::class => Agent::orderBy('name')->get(['id', 'name']),
            Pipeline::class => Pipeline::orderBy('name')->get(['id', 'name']),
            Endpoint::class => Endpoint::orderBy('name')->get(['id', 'name']),
            default => collect(),
        };
    }

    public function save(): void
    {
        $this->validate([
            'assessable_type' => 'required|string',
            'assessable_id' => 'required|integer',
            'risk_level' => 'required|in:minimal,limited,high',
            'use_case_description' => 'nullable|string',
            'affected_persons' => 'nullable|string',
            'mitigations' => 'nullable|string',
            'last_reviewed_at' => 'nullable|date',
            'reviewed_by' => 'nullable|string',
        ]);

        $data = [
            'assessable_type' => $this->assessable_type,
            'assessable_id' => $this->assessable_id,
            'risk_level' => $this->risk_level,
            'use_case_description' => $this->use_case_description ?: null,
            'affected_persons' => $this->affected_persons ?: null,
            'mitigations' => $this->mitigations ?: null,
            'last_reviewed_at' => $this->last_reviewed_at ?: null,
            'reviewed_by' => $this->reviewed_by ?: null,
        ];

        if ($this->assessmentId) {
            AiRiskAssessment::findOrFail($this->assessmentId)->update($data);
            session()->flash('success', 'Risk assessment updated.');
        } else {
            AiRiskAssessment::create($data);
            session()->flash('success', 'Risk assessment created.');
        }

        $this->redirect(route('admin.compliance.risk-assessments.index'), navigate: true);
    }

    public function render(): View
    {
        return view('agentoom-compliance::livewire.admin.compliance.risk-assessment-create-edit', [
            'assessableOptions' => $this->assessableOptions,
        ])->layout('agentoom-core::layouts.admin');
    }
}
