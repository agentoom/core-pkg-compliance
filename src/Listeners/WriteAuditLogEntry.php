<?php

namespace Agentoom\Compliance\Listeners;

use Agentoom\Compliance\Models\AiAuditLog;
use Agentoom\Core\Events\AiActionExecuted;

class WriteAuditLogEntry
{
    /**
     * Handle the event.
     */
    public function handle(AiActionExecuted $event): void
    {
        AiAuditLog::create([
            'entity_type' => $event->entityType,
            'entity_id' => $event->entityId,
            'event_type' => $event->eventType,
            'actor_id' => $event->actorId,
            'actor_type' => $event->actorType,
            'input_hash' => $event->input !== null ? hash('sha256', serialize($event->input)) : null,
            'output_hash' => $event->output !== null ? hash('sha256', serialize($event->output)) : null,
            'metadata' => $event->metadata ?: null,
        ]);
    }
}
