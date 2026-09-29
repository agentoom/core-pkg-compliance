<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\GovernanceProfile;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GovernanceProfileCreateEdit extends Component
{
    public ?int $profileId = null;

    public string $name = '';

    public string $description = '';

    public string $governance_level = 'basic';

    /** @var array<int, string> */
    public array $controls = [];

    public bool $active = true;

    /** @var array<string, string> */
    public array $governanceLevels = [
        'basic' => 'Basic',
        'standard' => 'Standard',
        'enhanced' => 'Enhanced',
    ];

    /** @var array<string, string> */
    public array $availableControls = [
        'audit_logs' => 'Audit Logs',
        'disclosure' => 'Disclosure',
        'evaluations' => 'Evaluations',
        'incidents' => 'Incidents',
        'periodic_reviews' => 'Periodic Reviews',
        'human_oversight' => 'Human Oversight',
        'monitoring' => 'Monitoring',
        'evidence_packages' => 'Evidence Packages',
    ];

    public function mount(?int $profileId = null): void
    {
        $this->profileId = $profileId;

        if ($profileId) {
            $profile = GovernanceProfile::findOrFail($profileId);
            $this->name = $profile->name;
            $this->description = $profile->description ?? '';
            $this->governance_level = $profile->governance_level;
            $this->controls = $profile->controls ?? [];
            $this->active = $profile->active;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'governance_level' => 'required|in:basic,standard,enhanced',
            'controls' => 'array',
            'controls.*' => 'string',
            'active' => 'boolean',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'governance_level' => $this->governance_level,
            'controls' => $this->controls,
            'active' => $this->active,
        ];

        if ($this->profileId) {
            GovernanceProfile::findOrFail($this->profileId)->update($data);
            session()->flash('success', __('agentoom-compliance::admin.compliance.profile_updated'));
        } else {
            GovernanceProfile::create($data);
            session()->flash('success', __('agentoom-compliance::admin.compliance.profile_created'));
        }

        $this->redirect(route('admin.compliance.governance-profiles.index'), navigate: true);
    }

    public function render(): View
    {
        return view('agentoom-compliance::livewire.admin.compliance.governance-profile-create-edit')
            ->layout('agentoom-core::layouts.admin');
    }
}
