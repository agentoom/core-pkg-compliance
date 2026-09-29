<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiEvaluation;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class EvaluationIndex extends Component
{
    use WithPagination;

    public function render(): View
    {
        $evaluations = AiEvaluation::latest()->paginate(15);

        $averageScore = AiEvaluation::whereNotNull('score')->avg('score');

        return view('agentoom-compliance::livewire.admin.compliance.evaluation-index', compact('evaluations', 'averageScore'))
            ->layout('agentoom-core::layouts.admin');
    }
}
