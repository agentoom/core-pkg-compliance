<?php

use Agentoom\Compliance\Livewire\Admin\AgentTraceIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\AuditLogIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\AuditTraceView;
use Agentoom\Compliance\Livewire\Admin\Compliance\ComplianceAlertIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\ComplianceAssessmentWizard;
use Agentoom\Compliance\Livewire\Admin\Compliance\ComplianceDashboard;
use Agentoom\Compliance\Livewire\Admin\Compliance\ComplianceReportIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\ComplianceReportShow;
use Agentoom\Compliance\Livewire\Admin\Compliance\EmergencyStopIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\EvaluationCreateEdit;
use Agentoom\Compliance\Livewire\Admin\Compliance\EvaluationIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\GovernanceControlIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\GovernanceProfileCreateEdit;
use Agentoom\Compliance\Livewire\Admin\Compliance\GovernanceProfileIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\GovernedDeploymentCreateEdit;
use Agentoom\Compliance\Livewire\Admin\Compliance\GovernedDeploymentIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\IncidentCreateEdit;
use Agentoom\Compliance\Livewire\Admin\Compliance\IncidentIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\PendingDecisionIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\RiskAssessmentCreateEdit;
use Agentoom\Compliance\Livewire\Admin\Compliance\RiskAssessmentIndex;
use Agentoom\Compliance\Livewire\Admin\Compliance\RiskAssessmentVersions;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['web', 'check.onboarding', 'admin'])->group(function () {
    Route::prefix('compliance')->name('compliance.')->group(function () {
        Route::get('dashboard', ComplianceDashboard::class)->name('dashboard');
        Route::get('audit-log', AuditLogIndex::class)->name('audit-log.index');
        Route::get('audit-log/trace/{traceId}', AuditTraceView::class)->name('audit-log.trace');
        Route::get('agent-traces', AgentTraceIndex::class)->name('agent-traces.index')->middleware('permission:agent-traces.view');
        Route::get('pending-decisions', PendingDecisionIndex::class)->name('pending-decisions.index');
        Route::get('incidents', IncidentIndex::class)->name('incidents.index');
        Route::get('incidents/create', IncidentCreateEdit::class)->name('incidents.create-edit');
        Route::get('incidents/{incidentId}/edit', IncidentCreateEdit::class)->name('incidents.edit');
        Route::get('risk-assessments', RiskAssessmentIndex::class)->name('risk-assessments.index');
        Route::get('risk-assessments/create', RiskAssessmentCreateEdit::class)->name('risk-assessments.create-edit');
        Route::get('risk-assessments/{assessmentId}/edit', RiskAssessmentCreateEdit::class)->name('risk-assessments.edit');
        Route::get('risk-assessments/{assessmentId}/versions', RiskAssessmentVersions::class)->name('risk-assessments.versions');
        Route::get('alerts', ComplianceAlertIndex::class)->name('alerts.index');
        Route::get('emergency-stops', EmergencyStopIndex::class)->name('emergency-stops.index');
        Route::get('evaluations', EvaluationIndex::class)->name('evaluations.index');
        Route::get('evaluations/create', EvaluationCreateEdit::class)->name('evaluations.create-edit');
        Route::get('evaluations/{evaluationId}/edit', EvaluationCreateEdit::class)->name('evaluations.edit');
        Route::get('reports', ComplianceReportIndex::class)->name('reports.index');
        Route::get('reports/{reportId}', ComplianceReportShow::class)->name('reports.show');
        Route::get('governed-deployments', GovernedDeploymentIndex::class)->name('governed-deployments.index');
        Route::get('governed-deployments/create', GovernedDeploymentCreateEdit::class)->name('governed-deployments.create');
        Route::get('governed-deployments/{deploymentId}/edit', GovernedDeploymentCreateEdit::class)->name('governed-deployments.edit');
        Route::get('assessment-wizard', ComplianceAssessmentWizard::class)->name('assessment-wizard');
        Route::get('governance-controls', GovernanceControlIndex::class)->name('governance-controls.index');
        Route::get('governance-profiles', GovernanceProfileIndex::class)->name('governance-profiles.index');
        Route::get('governance-profiles/create', GovernanceProfileCreateEdit::class)->name('governance-profiles.create');
        Route::get('governance-profiles/{profileId}/edit', GovernanceProfileCreateEdit::class)->name('governance-profiles.edit');
    });
});
