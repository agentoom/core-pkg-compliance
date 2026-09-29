<?php

namespace Agentoom\Compliance;

use Agentoom\Compliance\Helpers\ComplianceConfig;
use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Compliance\Services\AgentAuditor;
use Agentoom\Compliance\Services\ComplianceEventDispatcher;
use Agentoom\Compliance\Services\ComplianceGuard;
use Agentoom\Core\Models\AgentConversationMessage;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Responses\TextResponse;

/**
 * Real compliance capability bound over the core contract when this
 * package is installed. Delegates to the package's feature checks,
 * emergency-stop guard, audit dispatcher and agent auditor.
 */
class ComplianceIntegration implements \Agentoom\Core\Contracts\ComplianceIntegration
{
    public function isEnabled(): bool
    {
        return ComplianceConfig::isEnabled();
    }

    public function isFeatureEnabled(string $feature): bool
    {
        return ComplianceConfig::isFeatureEnabled($feature);
    }

    public function enabledFeatures(): array
    {
        return ComplianceConfig::enabledFeatures();
    }

    public function assertNotStopped(string $entityType, int|string $entityId): void
    {
        ComplianceGuard::assertNotStopped($entityType, $entityId);
    }

    public function dispatchAgentEvent(
        string $eventType,
        int $agentId,
        mixed $input = null,
        mixed $output = null,
        array $metadata = [],
        ?int $taskId = null,
        ?int $pipelineRunId = null,
        ?int $endpointId = null,
        ?int $scheduleLogId = null,
        ?int $userId = null,
    ): void {
        ComplianceEventDispatcher::dispatchAgentEvent(
            eventType: $eventType,
            agentId: $agentId,
            input: $input,
            output: $output,
            metadata: $metadata,
            taskId: $taskId,
            pipelineRunId: $pipelineRunId,
            endpointId: $endpointId,
            scheduleLogId: $scheduleLogId,
            userId: $userId,
        );
    }

    public function dispatchEndpointEvent(
        string $eventType,
        int $endpointId,
        int $agentId,
        mixed $input = null,
        mixed $output = null,
        array $metadata = [],
    ): void {
        ComplianceEventDispatcher::dispatchEndpointEvent(
            eventType: $eventType,
            endpointId: $endpointId,
            agentId: $agentId,
            input: $input,
            output: $output,
            metadata: $metadata,
        );
    }

    public function dispatchTaskEvent(
        string $eventType,
        int $taskId,
        int $agentId,
        mixed $input = null,
        mixed $output = null,
        array $metadata = [],
        ?int $userId = null,
    ): void {
        ComplianceEventDispatcher::dispatchTaskEvent(
            eventType: $eventType,
            taskId: $taskId,
            agentId: $agentId,
            input: $input,
            output: $output,
            metadata: $metadata,
            userId: $userId,
        );
    }

    public function traceAgentRun(
        Agent $agent,
        TextResponse $response,
        ?AgentConversationMessage $currentMessage = null,
        ?int $executionTimeMs = null,
        ?string $traceableType = null,
        ?int $traceableId = null,
    ): ?AiAgentTrace {
        return AgentAuditor::trace($agent, $response, $currentMessage, $executionTimeMs, $traceableType, $traceableId);
    }
}
