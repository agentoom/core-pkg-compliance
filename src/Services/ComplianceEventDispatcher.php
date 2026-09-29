<?php

namespace Agentoom\Compliance\Services;

use Agentoom\Compliance\Helpers\ComplianceConfig;
use Agentoom\Core\Events\AiActionExecuted;

class ComplianceEventDispatcher
{
    /**
     * Dispatch an AiActionExecuted event for an agent execution.
     *
     * Called after a successful or failed agent prompt. When audit logging is
     * disabled via compliance config, this method is a no-op.
     *
     * @param  'agent.run.completed'|'agent.run.failed'|'agent.run.paused'  $eventType
     */
    public static function dispatchAgentEvent(
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
        if (! ComplianceConfig::isFeatureEnabled('audit_logging')) {
            return;
        }

        $entityType = 'agent';
        $entityId = $agentId;

        // Enrich metadata with execution context
        if ($taskId) {
            $metadata['task_id'] = $taskId;
        }
        if ($pipelineRunId) {
            $metadata['pipeline_run_id'] = $pipelineRunId;
        }
        if ($endpointId) {
            $metadata['endpoint_id'] = $endpointId;
        }
        if ($scheduleLogId) {
            $metadata['schedule_log_id'] = $scheduleLogId;
        }

        AiActionExecuted::dispatch(
            entityType: $entityType,
            entityId: $entityId,
            eventType: $eventType,
            actorId: $userId ? (string) $userId : null,
            actorType: $userId ? 'user' : null,
            input: $input,
            output: $output,
            metadata: $metadata,
        );
    }

    /**
     * Dispatch an AiActionExecuted event for a pipeline step execution.
     *
     * @param  'pipeline.step.completed'|'pipeline.step.failed'  $eventType
     */
    public static function dispatchPipelineStepEvent(
        string $eventType,
        int $pipelineRunId,
        int $stepOrder,
        int $agentId,
        mixed $input = null,
        mixed $output = null,
        array $metadata = [],
    ): void {
        if (! ComplianceConfig::isFeatureEnabled('audit_logging')) {
            return;
        }

        $metadata['step_order'] = $stepOrder;
        $metadata['agent_id'] = $agentId;

        AiActionExecuted::dispatch(
            entityType: 'pipeline',
            entityId: $pipelineRunId,
            eventType: $eventType,
            input: $input,
            output: $output,
            metadata: $metadata,
        );
    }

    /**
     * Dispatch an AiActionExecuted event for an endpoint-triggered agent run.
     *
     * @param  'endpoint.processed'|'endpoint.failed'  $eventType
     */
    public static function dispatchEndpointEvent(
        string $eventType,
        int $endpointId,
        int $agentId,
        mixed $input = null,
        mixed $output = null,
        array $metadata = [],
    ): void {
        if (! ComplianceConfig::isFeatureEnabled('audit_logging')) {
            return;
        }

        $metadata['agent_id'] = $agentId;

        AiActionExecuted::dispatch(
            entityType: 'endpoint',
            entityId: $endpointId,
            eventType: $eventType,
            input: $input,
            output: $output,
            metadata: $metadata,
        );
    }

    /**
     * Dispatch an AiActionExecuted event for a task execution.
     *
     * @param  'task.completed'|'task.failed'|'task.paused'  $eventType
     */
    public static function dispatchTaskEvent(
        string $eventType,
        int $taskId,
        int $agentId,
        mixed $input = null,
        mixed $output = null,
        array $metadata = [],
        ?int $userId = null,
    ): void {
        if (! ComplianceConfig::isFeatureEnabled('audit_logging')) {
            return;
        }

        $metadata['agent_id'] = $agentId;

        AiActionExecuted::dispatch(
            entityType: 'task',
            entityId: $taskId,
            eventType: $eventType,
            actorId: $userId ? (string) $userId : null,
            actorType: $userId ? 'user' : null,
            input: $input,
            output: $output,
            metadata: $metadata,
        );
    }
}
