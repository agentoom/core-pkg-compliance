<?php

namespace Agentoom\Compliance\Database\Seeders;

use Agentoom\Compliance\Models\GovernanceControl;
use Agentoom\Compliance\Models\GovernanceProfile;
use Agentoom\Compliance\Models\GovernanceRecommendation;
use Illuminate\Database\Seeder;

class GovernanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProfiles();
        $this->seedRecommendations();
        $this->seedControls();
    }

    private function seedProfiles(): void
    {
        $profiles = [
            [
                'name' => 'Basic Governance',
                'description' => 'Suitable for low-impact AI deployments such as internal analytics, reporting, and automation.',
                'governance_level' => 'basic',
                'controls' => ['audit_logs', 'disclosure'],
                'active' => true,
            ],
            [
                'name' => 'Standard Governance',
                'description' => 'Suitable for AI deployments that interact with customers or process business data.',
                'governance_level' => 'standard',
                'controls' => ['audit_logs', 'evaluations', 'incidents', 'periodic_reviews', 'disclosure'],
                'active' => true,
            ],
            [
                'name' => 'Enhanced Governance',
                'description' => 'Suitable for AI deployments that affect individuals, process sensitive data, or operate with limited human oversight.',
                'governance_level' => 'enhanced',
                'controls' => ['audit_logs', 'human_oversight', 'evaluations', 'incidents', 'monitoring', 'periodic_reviews', 'disclosure'],
                'active' => true,
            ],
        ];

        foreach ($profiles as $profile) {
            GovernanceProfile::firstOrCreate(['name' => $profile['name']], $profile);
        }
    }

    private function seedRecommendations(): void
    {
        $recommendations = [
            ['trigger' => 'candidate_screening', 'recommendation' => 'enhanced', 'severity' => 'high', 'rationale' => 'Candidate screening directly affects employment decisions for individuals.'],
            ['trigger' => 'employee_evaluation', 'recommendation' => 'enhanced', 'severity' => 'high', 'rationale' => 'Employee evaluation affects livelihoods and requires strong oversight.'],
            ['trigger' => 'healthcare_support', 'recommendation' => 'enhanced', 'severity' => 'high', 'rationale' => 'Healthcare support involves sensitive health data and patient safety.'],
            ['trigger' => 'education_support', 'recommendation' => 'enhanced', 'severity' => 'high', 'rationale' => 'Education support may affect students including minors.'],
            ['trigger' => 'finance_decisions', 'recommendation' => 'enhanced', 'severity' => 'high', 'rationale' => 'Financial decisions affect individuals\' economic wellbeing.'],
            ['trigger' => 'customer_support', 'recommendation' => 'standard', 'severity' => 'medium', 'rationale' => 'Customer support interacts with external users and requires transparency.'],
            ['trigger' => 'sales_analysis', 'recommendation' => 'basic', 'severity' => 'low', 'rationale' => 'Internal sales analysis has limited direct impact on individuals.'],
            ['trigger' => 'inventory', 'recommendation' => 'basic', 'severity' => 'low', 'rationale' => 'Inventory management is an internal operational process.'],
            ['trigger' => 'accounting', 'recommendation' => 'standard', 'severity' => 'medium', 'rationale' => 'Accounting processes financial data requiring appropriate controls.'],
            ['trigger' => 'workflow_automation', 'recommendation' => 'basic', 'severity' => 'low', 'rationale' => 'General workflow automation has limited direct impact on individuals.'],
            ['trigger' => 'marketing', 'recommendation' => 'standard', 'severity' => 'medium', 'rationale' => 'Marketing AI processes customer data and influences consumer behaviour.'],
        ];

        foreach ($recommendations as $rec) {
            GovernanceRecommendation::firstOrCreate(['trigger' => $rec['trigger']], $rec);
        }
    }

    private function seedControls(): void
    {
        $controls = [
            // Agentoom controls
            ['framework' => 'agentoom', 'code' => 'AGT-01', 'title' => 'Audit Logging', 'description' => 'Immutable append-only audit logs for all AI actions via AiAuditLog.'],
            ['framework' => 'agentoom', 'code' => 'AGT-02', 'title' => 'Human Oversight', 'description' => 'Pending decisions queue with approve, reject, and override capabilities.'],
            ['framework' => 'agentoom', 'code' => 'AGT-03', 'title' => 'Risk Assessment', 'description' => 'Versioned risk assessments with review expiry and compliance alerts.'],
            ['framework' => 'agentoom', 'code' => 'AGT-04', 'title' => 'Incident Management', 'description' => 'Incident tracking with severity levels and authority notification records.'],
            ['framework' => 'agentoom', 'code' => 'AGT-05', 'title' => 'Evaluations', 'description' => 'Performance and accuracy evaluation records for AI deployments.'],
            ['framework' => 'agentoom', 'code' => 'AGT-06', 'title' => 'Emergency Stop', 'description' => 'Immediate halt capability for any Agent, Pipeline, or Endpoint.'],
            ['framework' => 'agentoom', 'code' => 'AGT-07', 'title' => 'Transparency Disclosure', 'description' => 'AI disclosure evidence stored per conversation with text and version.'],
            ['framework' => 'agentoom', 'code' => 'AGT-08', 'title' => 'Compliance Monitoring', 'description' => 'Post-market monitoring dashboard with period filters and trend widgets.'],
            ['framework' => 'agentoom', 'code' => 'AGT-09', 'title' => 'Evidence Packages', 'description' => 'One-click auditor export of all governance data for a deployment.'],
            ['framework' => 'agentoom', 'code' => 'AGT-10', 'title' => 'Governance Profiles', 'description' => 'Predefined governance control sets assignable to deployments.'],
            // ISO 42001 mappings
            ['framework' => 'iso_42001', 'code' => 'ISO-6.1', 'title' => 'Risk Assessment', 'description' => 'Maps to Agentoom AiRiskAssessment with versioning and review expiry.'],
            ['framework' => 'iso_42001', 'code' => 'ISO-8.4', 'title' => 'AI System Logging', 'description' => 'Maps to Agentoom AiAuditLog append-only record keeping.'],
            ['framework' => 'iso_42001', 'code' => 'ISO-9.1', 'title' => 'Monitoring and Measurement', 'description' => 'Maps to Agentoom Compliance Dashboard and post-market monitoring.'],
            // NIST AI RMF mappings
            ['framework' => 'nist_ai_rmf', 'code' => 'GOVERN-1', 'title' => 'Governance Policies', 'description' => 'Maps to Agentoom GovernanceProfile and GovernedDeployment.'],
            ['framework' => 'nist_ai_rmf', 'code' => 'MAP-1', 'title' => 'Context Establishment', 'description' => 'Maps to Agentoom ComplianceAssessment wizard.'],
            ['framework' => 'nist_ai_rmf', 'code' => 'MEASURE-2', 'title' => 'AI Risk Measurement', 'description' => 'Maps to Agentoom AiEvaluation and GovernanceReadinessService.'],
            ['framework' => 'nist_ai_rmf', 'code' => 'MANAGE-1', 'title' => 'Risk Treatment', 'description' => 'Maps to Agentoom AiRiskAssessment mitigations and AiComplianceAlert.'],
        ];

        foreach ($controls as $control) {
            GovernanceControl::firstOrCreate(
                ['framework' => $control['framework'], 'code' => $control['code']],
                $control
            );
        }
    }
}
