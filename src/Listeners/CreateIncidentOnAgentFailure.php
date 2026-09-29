<?php

namespace Agentoom\Compliance\Listeners;

use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Core\Jobs\ProcessAiTaskJob;
use Agentoom\Core\Jobs\RunPipelineJob;
use Illuminate\Queue\Events\JobFailed;

class CreateIncidentOnAgentFailure
{
    public function handle(JobFailed $event): void
    {
        // Resolve the actual job class name from the payload
        $payload = $event->job->payload();
        $resolvedClass = $payload['displayName'] ?? $payload['job'] ?? null;

        $watchedJobs = [
            ProcessAiTaskJob::class,
            RunPipelineJob::class,
        ];

        if (! in_array($resolvedClass, $watchedJobs, true)) {
            return;
        }

        AiIncident::create([
            'severity' => 'high',
            'description' => 'Automated incident: job '.class_basename($resolvedClass).' failed. '.$event->exception->getMessage(),
            'affected_entity_type' => $resolvedClass,
            'affected_entity_id' => $payload['id'] ?? null,
            'reported_by' => 'system',
            'status' => 'open',
        ]);
    }
}
