<?php

namespace Agentoom\Compliance\Services;

use Agentoom\Compliance\Helpers\ComplianceConfig;
use Agentoom\Compliance\Models\AiEmergencyStop;
use Agentoom\Core\Exceptions\EmergencyStopActiveException;

class ComplianceGuard
{
    /**
     * Assert that no emergency stop is active for the given entity.
     *
     * When compliance emergency stops are disabled (master switch or feature
     * toggle), this method is a no-op and never throws.
     *
     * @param  string  $entityType  e.g. 'agent', 'pipeline', 'task', 'endpoint'
     *
     * @throws EmergencyStopActiveException
     */
    public static function assertNotStopped(string $entityType, int|string $entityId): void
    {
        if (! ComplianceConfig::isFeatureEnabled('emergency_stops')) {
            return;
        }

        if (AiEmergencyStop::isStoppedFor($entityType, $entityId)) {
            throw new EmergencyStopActiveException($entityType, $entityId);
        }
    }
}
