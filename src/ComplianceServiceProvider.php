<?php

namespace Agentoom\Compliance;

use Agentoom\Compliance\Execution\GovernanceGate;
use Agentoom\Compliance\Listeners\CreateIncidentOnAgentFailure;
use Agentoom\Compliance\Listeners\WriteAuditLogEntry;
use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Compliance\Observers\AgentConversationObserver;
use Agentoom\Core\Contracts\ComplianceIntegration as ComplianceIntegrationContract;
use Agentoom\Core\Events\AiActionExecuted;
use Agentoom\Core\Execution\ExecutionPipeRegistry;
use Agentoom\Core\Models\AgentConversation;
use Agentoom\Core\Services\PermissionRegistry;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ComplianceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/agentoom-compliance.php', 'agentoom-compliance');

        // Rebind the core no-op default with the real compliance capability.
        $this->app->singleton(ComplianceIntegrationContract::class, ComplianceIntegration::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'agentoom-compliance');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'agentoom-compliance');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Livewire::addNamespace('agentoom-compliance', null, 'Agentoom\\Compliance\\Livewire');

        $this->app->make(PermissionRegistry::class)->register('agent_traces', ['agent-traces.view']);

        // The traces relation lives with the trace model in this package;
        // attach it to the core conversation model at boot time.
        AgentConversation::resolveRelationUsing('traces', function (AgentConversation $conversation) {
            return $conversation->hasMany(AiAgentTrace::class, 'conversation_id');
        });

        // Governance gate pipe in the core execution pipeline.
        $this->app->make(ExecutionPipeRegistry::class)->register(GovernanceGate::class);

        // Audit logging listener (gated by the audit_logging feature flag
        // at dispatch time) and auto-incident listener.
        Event::listen(AiActionExecuted::class, WriteAuditLogEntry::class);
        Event::listen(JobFailed::class, CreateIncidentOnAgentFailure::class);

        // AI disclosure stamping on conversations.
        AgentConversation::observe(AgentConversationObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\GenerateComplianceAlerts::class,
                Console\Commands\GenerateComplianceReport::class,
            ]);

            Schedule::command('compliance:generate-alerts')->daily();
        }
    }
}
