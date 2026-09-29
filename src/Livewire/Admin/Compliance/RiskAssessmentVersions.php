<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiRiskAssessment;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RiskAssessmentVersions extends Component
{
    public int $assessmentId;

    public function mount(int $assessmentId): void
    {
        $this->assessmentId = $assessmentId;
    }

    public function render(): View
    {
        $assessment = AiRiskAssessment::with('versions')->findOrFail($this->assessmentId);

        return view('agentoom-compliance::livewire.admin.compliance.risk-assessment-versions', compact('assessment'))
            ->layout('agentoom-core::layouts.admin');
    }
}
