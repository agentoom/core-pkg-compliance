<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiComplianceReport;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComplianceReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_compliance_report_command_creates_report(): void
    {
        $this->artisan('ai:generate-compliance-report', ['--frequency' => 'monthly'])
            ->assertSuccessful();

        $this->assertDatabaseCount('ai_compliance_reports', 1);

        $report = AiComplianceReport::first();
        $this->assertEquals('monthly', $report->frequency);
        $this->assertNotNull($report->summary);
        $this->assertArrayHasKey('incidents', $report->summary);
        $this->assertArrayHasKey('risk_assessments', $report->summary);
        $this->assertArrayHasKey('audit_activity', $report->summary);
        $this->assertArrayHasKey('human_interventions', $report->summary);
        $this->assertArrayHasKey('evaluation_scores', $report->summary);
    }

    public function test_generate_compliance_report_weekly(): void
    {
        $this->artisan('ai:generate-compliance-report', ['--frequency' => 'weekly'])
            ->assertSuccessful();

        $report = AiComplianceReport::first();
        $this->assertEquals('weekly', $report->frequency);
        $this->assertStringContainsString('Weekly', $report->title);
    }

    public function test_generate_compliance_report_quarterly(): void
    {
        $this->artisan('ai:generate-compliance-report', ['--frequency' => 'quarterly'])
            ->assertSuccessful();

        $report = AiComplianceReport::first();
        $this->assertEquals('quarterly', $report->frequency);
    }
}
