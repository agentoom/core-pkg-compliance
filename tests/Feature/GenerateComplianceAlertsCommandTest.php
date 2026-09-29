<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateComplianceAlertsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_is_registered(): void
    {
        $this->artisan('compliance:generate-alerts --help')
            ->assertSuccessful();
    }

    public function test_command_runs_successfully(): void
    {
        $this->artisan('compliance:generate-alerts')
            ->assertSuccessful();
    }
}
