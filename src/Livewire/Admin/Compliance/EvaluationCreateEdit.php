<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiEvaluation;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\Endpoint;
use Agentoom\Core\Models\Pipeline;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EvaluationCreateEdit extends Component
{
    public ?int $evaluationId = null;

    public string $entity_type = '';

    public string $entity_id = '';

    public string $dataset_name = '';

    public string $score = '';

    public string $metric = '';

    public string $notes = '';

    public string $evaluated_by = '';

    /** @var array<string, string> */
    public array $assessableTypes = [];

    /** @var array<int, mixed> */
    public array $entityOptions = [];

    public function mount(?int $evaluationId = null): void
    {
        $this->assessableTypes = [
            Agent::class => 'Agent',
            Pipeline::class => 'Pipeline',
            Endpoint::class => 'Endpoint',
        ];

        $this->evaluationId = $evaluationId;

        if ($evaluationId) {
            $evaluation = AiEvaluation::findOrFail($evaluationId);
            $this->entity_type = $evaluation->entity_type;
            $this->entity_id = (string) $evaluation->entity_id;
            $this->dataset_name = $evaluation->dataset_name ?? '';
            $this->score = $evaluation->score !== null ? (string) $evaluation->score : '';
            $this->metric = $evaluation->metric;
            $this->notes = $evaluation->notes ?? '';
            $this->evaluated_by = $evaluation->evaluated_by ?? '';
        } else {
            $this->entity_type = Agent::class;
            $this->evaluated_by = auth()->user()->name ?? '';
        }

        $this->loadEntityOptions();
    }

    public function updatedEntityType(): void
    {
        $this->entity_id = '';
        $this->loadEntityOptions();
    }

    private function loadEntityOptions(): void
    {
        $this->entityOptions = match ($this->entity_type) {
            Agent::class => Agent::orderBy('name')->get(['id', 'name'])->toArray(),
            Pipeline::class => Pipeline::orderBy('name')->get(['id', 'name'])->toArray(),
            Endpoint::class => Endpoint::orderBy('name')->get(['id', 'name'])->toArray(),
            default => [],
        };
    }

    public function save(): void
    {
        $this->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required',
            'metric' => 'required|string|max:255',
            'score' => 'nullable|numeric|min:0|max:100',
            'dataset_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'evaluated_by' => 'nullable|string|max:255',
        ]);

        $data = [
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'dataset_name' => $this->dataset_name ?: null,
            'score' => $this->score !== '' ? (float) $this->score : null,
            'metric' => $this->metric,
            'notes' => $this->notes ?: null,
            'evaluated_by' => $this->evaluated_by ?: null,
        ];

        if ($this->evaluationId) {
            AiEvaluation::findOrFail($this->evaluationId)->update($data);
            session()->flash('success', 'Evaluation updated.');
        } else {
            AiEvaluation::create($data);
            session()->flash('success', 'Evaluation recorded.');
        }

        $this->redirect(route('admin.compliance.evaluations.index'), navigate: true);
    }

    public function render(): View
    {
        return view('agentoom-compliance::livewire.admin.compliance.evaluation-create-edit', [
            'assessableTypes' => $this->assessableTypes,
            'entityOptions' => $this->entityOptions,
        ])->layout('agentoom-core::layouts.admin');
    }
}
