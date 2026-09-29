<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EU AI Act Compliance — Master Switch
    |--------------------------------------------------------------------------
    |
    | When disabled, ALL compliance runtime enforcement is bypassed.
    | Compliance UI pages remain accessible but no automatic enforcement
    | (audit logging, emergency stops, alerts, governance gates, disclosure)
    | will run. This is the recommended setting for non-EU deployments.
    |
    */
    'enabled' => env('EU_COMPLIANCE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Individual Feature Toggles
    |--------------------------------------------------------------------------
    |
    | Each feature can be independently enabled/disabled. These only take
    | effect when the master switch (EU_COMPLIANCE_ENABLED) is true.
    | When the master switch is false, all features are effectively off
    | regardless of their individual settings.
    |
    */
    'features' => [

        /*
        | Audit logging: dispatches AiActionExecuted events from all agent
        | execution entry points, creating tamper-evident audit log entries.
        */
        'audit_logging' => env('COMPLIANCE_AUDIT_LOGGING', true),

        /*
        | Emergency stops: checks AiEmergencyStop records at runtime before
        | allowing agent execution. If an active stop exists for the agent,
        | pipeline, or endpoint, execution is blocked.
        */
        'emergency_stops' => env('COMPLIANCE_EMERGENCY_STOPS', true),

        /*
        | Auto-incidents: when a ProcessAiTaskJob or RunPipelineJob fails,
        | automatically creates an AiIncident record for the compliance team.
        | This is listener-based (CreateIncidentOnAgentFailure) and only
        | fires when enabled.
        */
        'auto_incidents' => env('COMPLIANCE_AUTO_INCIDENTS', true),

        /*
        | Auto-alerts: a daily scheduled command scans for overdue risk
        | assessment reviews, high-risk agents without mitigations, and
        | elevated incident rates, and generates AiComplianceAlert records.
        */
        'auto_alerts' => env('COMPLIANCE_AUTO_ALERTS', true),

        /*
        | Governance gate: a pipe in the execution pipeline that checks
        | whether the agent has a governed deployment with an appropriate
        | governance profile for its risk level. Blocks execution if the
        | agent exceeds its governance profile's threshold.
        */
        'governance_gate' => env('COMPLIANCE_GOVERNANCE_GATE', true),

        /*
        | AI disclosure: stamps AI-generated content with a disclosure
        | notice in conversations when ai_disclosure_enabled config is on.
        | Controlled via AgentConversationObserver.
        */
        'ai_disclosure' => env('COMPLIANCE_AI_DISCLOSURE', true),

        /*
        | Risk enforcement: enforces risk assessment rules at runtime.
        | When enabled, agents with 'potential_high_risk' assessments
        | and no documented mitigations are blocked from execution.
        */
        'risk_enforcement' => env('COMPLIANCE_RISK_ENFORCEMENT', true),

    ],
];
