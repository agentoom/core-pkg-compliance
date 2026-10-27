# Agentoom Compliance (`agentoom/core-compliance`)

The enterprise governance, risk management, and EU AI Act compliance engine for **Agentoom**.

---

> [!IMPORTANT]
> **Post-Splitting Versioning & Pre-Release Notice (`v0.8.0`)**
> Version `0.8.0` represents the initial standalone release following the modular package extraction and architectural splitting from the Agentoom core monorepo. This package is currently in a pre-release state undergoing integration testing and requires appropriate validation and test coverage across target environments before being designated as production-stable (`1.0.0`).

## 🌟 Overview

The `agentoom/core-compliance` package equips Agentoom with regulatory compliance frameworks, real-time safety guardrails, execution auditing, and accountability controls required by the European Union AI Act and international AI governance standards. It plugs into the core execution pipeline to enforce risk assessments, human oversight checkpoints, emergency shutdown switches, and tamper-evident audit logs.

- **Package Name**: `agentoom/core-compliance`
- **Version**: `0.8.0` (Pre-release / Post-split testing)
- **Namespace**: `Agentoom\Compliance\`
- **Service Provider**: `Agentoom\Compliance\ComplianceServiceProvider`

---

## 🚀 Key Features

### 1. EU AI Act Risk Classification & Governance
- **Automated Risk Assessment**: Classifies AI agents into regulatory risk tiers (Minimal, Specific Transparency, High Risk, Prohibited) via `ComplianceClassificationService` and `AiRiskAssessment`.
- **Assessment Versioning & Review Expiry**: Full version history (`AiRiskAssessmentVersion`) to track risk reviews over time, including mandatory re-assessment triggers and expiration dates.
- **Governance Profiles & Controls**: Assign structured governance profiles (`GovernanceProfile`), measurable controls (`GovernanceControl`), and automated recommendations (`GovernanceRecommendation`) to agents and deployments.

### 2. Real-Time Governance Gates & Human-in-the-Loop
- **Governance Gate Pipeline**: Intercepts agent actions before execution (`GovernanceGate` execution pipe) to verify compliance readiness.
- **Pending Human Decisions**: Halts high-stakes actions and prompts human supervisors for approval or rejection (`AiPendingDecision`), complete with rationale auditing and override capabilities.
- **Emergency Stop / Kill Switch**: Instant suspension mechanism (`AiEmergencyStop`) that halts rogue or compromised agents immediately across all channels.

### 3. Comprehensive Traceability & Tamper-Evident Audit Logs
- **Execution Traces**: Granular, step-by-step recording of prompts, tool calls, parameters, response tokens, and latency (`AiAgentTrace`).
- **Cryptographic Audit Logs**: Immutable system events (`AiAuditLog`, `AgentAuditor`) recording administrative actions, parameter overrides, and agent configuration changes.
- **Disclosure Evidence**: Tracks whether mandatory AI disclosure banners and disclaimers were presented to end users.

### 4. Incident Management & Automated Alerts
- **Incident Tracking**: Automatically generates incident records when agents experience critical failures or ethical breaches (`AiIncident`, `CreateIncidentOnAgentFailure`).
- **Rule-Based Alert Generator**: Scans system telemetry to detect threshold breaches, unapproved actions, and expired assessments (`AiComplianceAlert`, `ComplianceAlertGenerator`).

### 5. Compliance Reports & Evidence Packages
- **Automated Report Generation**: Compile comprehensive compliance audit reports (`AiComplianceReport`) for regulatory authorities.
- **Evidence Bundling**: Packages traces, logs, policies, and test evaluations into portable compliance packages (`EvidencePackage`).

---

## 🏛️ Integration with Agentoom Core

`core-compliance` registers `ComplianceIntegration` in the service container, hooking directly into `core-main`'s `ActionExecutor`:

1. **Pre-Execution Check**: Before an agent executes a tool or pipeline step, `GovernanceGate` verifies whether the agent's risk level or pending decision requirements allow execution.
2. **Audit Listener**: `WriteAuditLogEntry` listens for `AiActionExecuted` events emitted by `core-main` and writes tamper-evident audit records.
3. **Emergency Stop Interceptor**: Ensures any agent marked under an active emergency stop cannot initiate or continue any task or chat.

---

## 📁 Package Structure

```
core-compliance/
├── config/                 # Compliance configuration defaults
├── database/
│   ├── migrations/         # Schemas for traces, audit logs, risk assessments, alerts
│   └── seeders/            # Governance baseline seeders
├── resources/
│   ├── lang/               # Multi-language translations (de, en, es, fr, it)
│   └── views/livewire/     # Compliance dashboard and wizard views
├── routes/
│   └── web.php             # Compliance admin routes
└── src/
    ├── Console/Commands/   # CLI commands for alerts and reports
    ├── Execution/          # GovernanceGate execution pipe
    ├── Helpers/            # ComplianceConfig helpers
    ├── Listeners/          # WriteAuditLogEntry, CreateIncidentOnAgentFailure
    ├── Livewire/Admin/     # Risk assessments, audits, traces, incident views
    ├── Models/             # Eloquent models (AiRiskAssessment, AiAgentTrace, etc.)
    ├── Observers/          # AgentConversationObserver
    ├── Services/           # Classification, Guard, Auditor, AlertGenerator
    └── ComplianceServiceProvider.php
```

---

## 💻 Console Commands

```bash
# Scan system traces and generate compliance alerts based on configured rules
php artisan compliance:generate-alerts

# Generate formal compliance and governance reports for auditors
php artisan compliance:report
```
