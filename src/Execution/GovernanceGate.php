<?php

namespace Agentoom\Compliance\Execution;

use Agentoom\Compliance\Helpers\ComplianceConfig;
use Agentoom\Compliance\Models\AiPendingDecision;
use Agentoom\Compliance\Models\GovernedDeployment;
use Agentoom\Core\Ai\Agents\DynamicAgent;
use Agentoom\Core\Exceptions\EmergencyStopActiveException;
use Agentoom\Core\Execution\ActionExecution;
use Closure;
use Illuminate\Support\Facades\Log;

class GovernanceGate
{
    /**
     * Governance levels that are always allowed regardless of profile configuration.
     * These represent the EU AI Act risk categories from lowest to highest.
     */
    protected const GOVERNANCE_LEVEL_SEVERITY = [
        'minimal' => 0,
        'limited' => 1,
        'potential_high_risk' => 2,
        'high_risk' => 3,
        'unacceptable' => 4,
    ];

    /**
     * Handle the action execution pipe.
     *
     * When governance gate is disabled (master switch or feature toggle),
     * the action passes through immediately.
     *
     * When enabled, this pipe checks:
     * 1. Whether the agent has a governed deployment with governance profile
     * 2. Whether there are pending HITL decisions requiring resolution
     * 3. Whether the governance level is within acceptable bounds (risk enforcement)
     *
     * If any check fails, execution is blocked with an EmergencyStopActiveException.
     */
    public function handle(ActionExecution $action, Closure $next): mixed
    {
        if (! ComplianceConfig::isFeatureEnabled('governance_gate')) {
            return $next($action);
        }

        // Only enforce governance for DynamicAgent (which wraps AgentModel)
        if (! ($action->agent instanceof DynamicAgent)) {
            return $next($action);
        }

        $agentModel = $action->agent->getAgentModel();
        $agentId = $agentModel->id;

        // Find governed deployment for this agent (polymorphic: deployable_type/agent, deployable_id/agentId)
        $deployment = GovernedDeployment::where('deployable_type', 'agent')
            ->where('deployable_id', $agentId)
            ->first();

        if (! $deployment) {
            return $next($action); // No governance configured — allow through
        }

        // Check: Does the governance profile exist?
        $profile = $deployment->governanceProfile;
        if (! $profile) {
            Log::warning("GovernanceGate: Agent #{$agentId} has governed deployment #{$deployment->id} but no governance profile.", [
                'agent_id' => $agentId,
                'deployment_id' => $deployment->id,
            ]);

            return $next($action);
        }

        // Risk enforcement: check governance level severity
        if (ComplianceConfig::isFeatureEnabled('risk_enforcement')) {
            $deploymentLevel = $deployment->classification_governance_level ?? 'minimal';
            $profileLevel = $profile->governance_level ?? 'limited';

            $deploymentSeverity = self::GOVERNANCE_LEVEL_SEVERITY[$deploymentLevel] ?? 0;
            $profileSeverity = self::GOVERNANCE_LEVEL_SEVERITY[$profileLevel] ?? 1;

            // Block if the deployment's governance level exceeds what the profile allows
            if ($deploymentSeverity > $profileSeverity) {
                Log::warning("GovernanceGate: Agent #{$agentId} governance level '{$deploymentLevel}' exceeds profile #{$profile->id} level '{$profileLevel}'.", [
                    'agent_id' => $agentId,
                    'deployment_level' => $deploymentLevel,
                    'profile_level' => $profileLevel,
                    'profile_id' => $profile->id,
                ]);

                throw new EmergencyStopActiveException('agent', $agentId);
            }
        }

        // Check for pending decisions that require resolution
        $hasPendingDecisions = AiPendingDecision::where('entity_type', 'agent')
            ->where('entity_id', $agentId)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingDecisions) {
            Log::warning("GovernanceGate: Agent #{$agentId} has pending HITL decisions requiring resolution.", [
                'agent_id' => $agentId,
                'deployment_id' => $deployment->id,
            ]);

            throw new EmergencyStopActiveException('agent', $agentId);
        }

        return $next($action);
    }
}
