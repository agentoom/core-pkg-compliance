<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\GovernanceProfile;
use Agentoom\Compliance\Models\GovernedDeployment;
use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\Endpoint;
use Agentoom\Core\Models\Pipeline;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class GovernedDeploymentCreateEdit extends Component
{
    public ?int $deploymentId = null;

    public string $name = '';

    public string $businessPurpose = '';

    public string $deployableType = '';

    public string $deployableId = '';

    public ?int $governanceProfileId = null;

    public ?array $classificationResult = null;

    /** @var array<string, string> */
    public array $deployableTypes = [
        'Agentoom\\Core\\Models\\Agent' => 'Agent',
        'Agentoom\\Core\\Models\\Pipeline' => 'Pipeline',
        'Agentoom\\Core\\Models\\Endpoint' => 'Endpoint',
    ];

    public function updatedDeployableType(): void
    {
        $this->deployableId = '';
    }

    public function getDeployableOptionsProperty(): EloquentCollection|Collection
    {
        return match ($this->deployableType) {
            Agent::class => Agent::orderBy('name')->get(['id', 'name']),
            Pipeline::class => Pipeline::orderBy('name')->get(['id', 'name']),
            Endpoint::class => Endpoint::orderBy('name')->get(['id', 'name']),
            default => collect(),
        };
    }

    public function mount(?int $deploymentId = null): void
    {
        $this->deploymentId = $deploymentId;

        if ($deploymentId) {
            $deployment = GovernedDeployment::findOrFail($deploymentId);
            $this->name = $deployment->name;
            $this->businessPurpose = $deployment->business_purpose ?? '';
            $this->deployableType = $deployment->deployable_type ?? '';
            $this->deployableId = (string) ($deployment->deployable_id ?? '');
            $this->governanceProfileId = $deployment->governance_profile_id;
            $this->classificationResult = $deployment->classification_governance_level ? [
                'governance_level' => $deployment->classification_governance_level,
                'potentially_sensitive' => $deployment->classification_potentially_sensitive,
                'recommended_controls' => $deployment->classification_recommended_controls ?? [],
                'reasoning' => $deployment->classification_reasoning,
            ] : null;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'businessPurpose' => 'nullable|string',
            'deployableType' => 'nullable|string|max:255',
            'deployableId' => 'nullable|integer',
            'governanceProfileId' => 'nullable|exists:governance_profiles,id',
        ]);

        $data = [
            'name' => $this->name,
            'business_purpose' => $this->businessPurpose ?: null,
            'deployable_type' => $this->deployableType ?: null,
            'deployable_id' => $this->deployableId ?: null,
            'governance_profile_id' => $this->governanceProfileId,
        ];

        if ($this->deploymentId) {
            GovernedDeployment::findOrFail($this->deploymentId)->update($data);
        } else {
            GovernedDeployment::create($data);
        }

        $this->redirect(route('admin.compliance.governed-deployments.index'));
    }

    public function render(): View
    {
        $profiles = GovernanceProfile::where('active', true)->orderBy('name')->get();

        return view('agentoom-compliance::livewire.admin.compliance.governed-deployment-create-edit', [
            'profiles' => $profiles,
            'deployableOptions' => $this->deployableOptions,
        ])->layout('agentoom-core::layouts.admin');
    }
}
