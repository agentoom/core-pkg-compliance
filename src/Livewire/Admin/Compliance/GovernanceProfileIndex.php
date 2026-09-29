<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\GovernanceProfile;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class GovernanceProfileIndex extends Component
{
    use WithPagination;

    public function delete(int $id): void
    {
        $profile = GovernanceProfile::findOrFail($id);

        if ($profile->governedDeployments()->exists()) {
            session()->flash('error', __('agentoom-compliance::admin.compliance.profile_delete_error'));

            return;
        }

        $profile->delete();
        session()->flash('success', __('agentoom-compliance::admin.compliance.profile_deleted'));
    }

    public function toggleActive(int $id): void
    {
        $profile = GovernanceProfile::findOrFail($id);
        $profile->update(['active' => ! $profile->active]);
    }

    public function render(): View
    {
        $profiles = GovernanceProfile::orderBy('governance_level')->orderBy('name')->paginate(15);

        return view('agentoom-compliance::livewire.admin.compliance.governance-profile-index', compact('profiles'))
            ->layout('agentoom-core::layouts.admin');
    }
}
