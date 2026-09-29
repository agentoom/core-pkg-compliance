<?php

namespace Agentoom\Compliance\Console\Commands;

use Agentoom\Compliance\Services\ComplianceAlertGenerator;
use Illuminate\Console\Command;

class GenerateComplianceAlerts extends Command
{
    protected $signature = 'compliance:generate-alerts';

    protected $description = 'Scan for compliance issues and generate alerts (overdue reviews, high-risk without mitigations, incident rate)';

    public function handle(ComplianceAlertGenerator $generator): int
    {
        $count = $generator->generate();

        if ($count > 0) {
            $this->info("Generated {$count} compliance alert(s).");
        } else {
            $this->info('No new compliance alerts generated.');
        }

        return self::SUCCESS;
    }
}
