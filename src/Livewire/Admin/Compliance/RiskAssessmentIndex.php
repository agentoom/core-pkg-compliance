<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiRiskAssessment;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RiskAssessmentIndex extends Component
{
    use WithPagination;

    public function render(): View
    {
        $assessments = AiRiskAssessment::latest()->paginate(15);

        return view('agentoom-compliance::livewire.admin.compliance.risk-assessment-index', compact('assessments'))->layout('agentoom-core::layouts.admin');
    }
}
