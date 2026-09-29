<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Compliance\Services\ComplianceAlertGenerator;
use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;

class ComplianceAlertGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
    }

    public function test_generates_alerts_for_overdue_reviews(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.auto_alerts', true);

        AiRiskAssessment::create([
            'assessable_type' => 'agent',
            'assessable_id' => 1,
            'risk_level' => 'limited',
            'review_interval_days' => 30,
            'last_reviewed_at' => now()->subDays(60),
            'next_review_due_at' => now()->subDays(30),
        ]);

        $generator = new ComplianceAlertGenerator;
        $count = $generator->generate();

        $this->assertGreaterThan(0, $count);
        $this->assertDatabaseHas('ai_compliance_alerts', [
            'type' => 'overdue_review',
            'entity_type' => 'risk_assessment',
            'status' => 'open',
        ]);
    }

    public function test_no_duplicate_alerts_for_same_overdue_review(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.auto_alerts', true);

        AiRiskAssessment::create([
            'assessable_type' => 'agent',
            'assessable_id' => 1,
            'risk_level' => 'limited',
            'next_review_due_at' => now()->subDays(10),
        ]);

        $generator = new ComplianceAlertGenerator;

        $firstCount = $generator->generate();
        $this->assertGreaterThan(0, $firstCount);

        $secondCount = $generator->generate();
        $this->assertEquals(0, $secondCount);
    }

    public function test_generates_alerts_for_high_risk_without_mitigations(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.auto_alerts', true);

        AiRiskAssessment::create([
            'assessable_type' => 'agent',
            'assessable_id' => 2,
            'risk_level' => 'potential_high_risk',
            'mitigations' => null,
        ]);

        $generator = new ComplianceAlertGenerator;
        $count = $generator->generate();

        $this->assertGreaterThan(0, $count);
        $this->assertDatabaseHas('ai_compliance_alerts', [
            'type' => 'high_risk_no_mitigations',
            'status' => 'open',
        ]);
    }

    public function test_zero_activity_generates_no_alerts(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.auto_alerts', true);

        $generator = new ComplianceAlertGenerator;
        $count = $generator->generate();

        $this->assertEquals(0, $count);
        $this->assertDatabaseCount('ai_compliance_alerts', 0);
    }

    public function test_auto_alerts_disabled_via_feature_toggle(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.auto_alerts', false);

        AiRiskAssessment::create([
            'assessable_type' => 'agent',
            'assessable_id' => 1,
            'risk_level' => 'limited',
            'next_review_due_at' => now()->subDays(30),
        ]);

        $generator = new ComplianceAlertGenerator;
        $count = $generator->generate();

        $this->assertEquals(0, $count);
    }

    public function test_high_incident_rate_triggers_alert(): void
    {
        Config::set('agentoom-compliance.enabled', true);
        Config::set('agentoom-compliance.features.auto_alerts', true);

        for ($i = 0; $i < 6; $i++) {
            AiIncident::create([
                'severity' => 'high',
                'description' => "Test incident {$i}",
                'status' => 'open',
                'reported_by' => 'system',
                'created_at' => now()->subDay(),
            ]);
        }

        $generator = new ComplianceAlertGenerator;
        $count = $generator->generate();

        $this->assertGreaterThan(0, $count);
        $this->assertDatabaseHas('ai_compliance_alerts', [
            'type' => 'high_incident_rate',
            'status' => 'open',
        ]);
    }
}
