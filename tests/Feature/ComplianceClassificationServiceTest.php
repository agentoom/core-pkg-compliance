<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Services\ComplianceClassificationService;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComplianceClassificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ComplianceClassificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ComplianceClassificationService;
    }

    public function test_basic_governance_for_low_impact_use_case(): void
    {
        $result = $this->service->classify([
            'ai_purpose' => 'sales_analysis',
            'ai_actions' => [],
            'affected_groups' => [],
            'human_review_frequency' => 'always',
            'data_processed' => ['business_data'],
        ]);

        $this->assertEquals('basic', $result['governance_level']);
        $this->assertFalse($result['potentially_sensitive']);
        $this->assertContains('audit_logs', $result['recommended_controls']);
    }

    public function test_enhanced_governance_for_candidate_screening(): void
    {
        $result = $this->service->classify([
            'ai_purpose' => 'candidate_screening',
            'ai_actions' => ['approve_reject_people'],
            'affected_groups' => ['applicants'],
            'human_review_frequency' => 'sometimes',
            'data_processed' => ['employee_data'],
        ]);

        $this->assertEquals('enhanced', $result['governance_level']);
        $this->assertTrue($result['potentially_sensitive']);
        $this->assertContains('human_oversight', $result['recommended_controls']);
        $this->assertContains('evaluations', $result['recommended_controls']);
    }

    public function test_enhanced_governance_for_healthcare(): void
    {
        $result = $this->service->classify([
            'ai_purpose' => 'healthcare_support',
            'ai_actions' => [],
            'affected_groups' => ['patients'],
            'human_review_frequency' => 'always',
            'data_processed' => ['health_data'],
        ]);

        $this->assertEquals('enhanced', $result['governance_level']);
        $this->assertTrue($result['potentially_sensitive']);
    }

    public function test_standard_governance_for_customer_support(): void
    {
        $result = $this->service->classify([
            'ai_purpose' => 'customer_support',
            'ai_actions' => ['recommend_actions'],
            'affected_groups' => ['customers'],
            'human_review_frequency' => 'usually',
            'data_processed' => ['customer_data'],
        ]);

        $this->assertContains($result['governance_level'], ['basic', 'standard', 'enhanced']);
    }

    public function test_no_human_review_escalates_to_enhanced(): void
    {
        $result = $this->service->classify([
            'ai_purpose' => 'workflow_automation',
            'ai_actions' => [],
            'affected_groups' => [],
            'human_review_frequency' => 'never',
            'data_processed' => [],
        ]);

        $this->assertEquals('enhanced', $result['governance_level']);
        $this->assertContains('monitoring', $result['recommended_controls']);
    }

    public function test_sensitive_data_escalates_governance(): void
    {
        $result = $this->service->classify([
            'ai_purpose' => 'workflow_automation',
            'ai_actions' => [],
            'affected_groups' => [],
            'human_review_frequency' => 'always',
            'data_processed' => ['health_data'],
        ]);

        $this->assertContains($result['governance_level'], ['basic', 'standard', 'enhanced']);
        $this->assertTrue($result['potentially_sensitive']);
        $this->assertContains('disclosure', $result['recommended_controls']);
    }

    public function test_result_contains_reasoning(): void
    {
        $result = $this->service->classify([
            'ai_purpose' => 'employee_evaluation',
            'ai_actions' => ['score_people'],
            'affected_groups' => ['employees'],
            'human_review_frequency' => 'sometimes',
            'data_processed' => ['employee_data'],
        ]);

        $this->assertNotEmpty($result['reasoning']);
        $this->assertIsString($result['reasoning']);
    }

    public function test_governance_level_never_downgrades(): void
    {
        // Enhanced trigger + basic data should stay enhanced
        $result = $this->service->classify([
            'ai_purpose' => 'candidate_screening',
            'ai_actions' => [],
            'affected_groups' => [],
            'human_review_frequency' => 'always',
            'data_processed' => ['business_data'],
        ]);

        $this->assertEquals('enhanced', $result['governance_level']);
    }
}
