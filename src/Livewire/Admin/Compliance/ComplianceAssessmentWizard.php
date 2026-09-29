<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\ComplianceAssessment;
use Agentoom\Compliance\Models\GovernanceProfile;
use Agentoom\Compliance\Services\ComplianceClassificationService;
use Illuminate\View\View;
use Livewire\Component;

class ComplianceAssessmentWizard extends Component
{
    public int $step = 1;

    public string $name = '';

    public string $aiPurpose = '';

    public array $aiActions = [];

    public array $affectedGroups = [];

    public string $humanReviewFrequency = 'always';

    public array $dataProcessed = [];

    public ?int $agentId = null;

    public ?array $classificationResult = null;

    public ?int $savedAssessmentId = null;

    public function nextStep(): void
    {
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function classify(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'aiPurpose' => 'required|string',
        ]);

        $service = app(ComplianceClassificationService::class);

        $this->classificationResult = $service->classify([
            'ai_purpose' => $this->aiPurpose,
            'ai_actions' => $this->aiActions,
            'affected_groups' => $this->affectedGroups,
            'human_review_frequency' => $this->humanReviewFrequency,
            'data_processed' => $this->dataProcessed,
        ]);

        $this->step = 3;
    }

    public function save(): void
    {
        if (! $this->classificationResult) {
            return;
        }

        $profile = GovernanceProfile::where('governance_level', $this->classificationResult['governance_level'])
            ->where('active', true)
            ->first();

        $assessment = ComplianceAssessment::create([
            'agent_id' => $this->agentId,
            'name' => $this->name,
            'ai_purpose' => $this->aiPurpose,
            'ai_actions' => $this->aiActions,
            'affected_groups' => $this->affectedGroups,
            'human_review_frequency' => $this->humanReviewFrequency,
            'data_processed' => $this->dataProcessed,
            'governance_level' => $this->classificationResult['governance_level'],
            'potentially_sensitive' => $this->classificationResult['potentially_sensitive'],
            'recommended_controls' => $this->classificationResult['recommended_controls'],
            'reasoning' => $this->classificationResult['reasoning'],
            'governance_profile_id' => $profile?->id,
        ]);

        $this->savedAssessmentId = $assessment->id;
        $this->step = 4;
    }

    public function render(): View
    {
        return view('agentoom-compliance::livewire.admin.compliance.compliance-assessment-wizard')
            ->layout('agentoom-core::layouts.admin');
    }
}
