<?php

namespace Agentoom\Compliance\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class AiAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'actor_id',
        'actor_type',
        'event_type',
        'input_hash',
        'output_hash',
        'metadata',
        'model_provider',
        'model_name',
        'model_version',
        'workflow_version',
        'prompt_version',
        'execution_duration_ms',
        'tool_name',
        'tool_arguments_hash',
        'tool_result_hash',
        'trace_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiAuditLog $log) {
            $log->created_at = now();
        });
    }

    public function save(array $options = []): bool
    {
        if ($this->exists) {
            throw new LogicException('AiAuditLog records are append-only and cannot be updated.');
        }

        return parent::save($options);
    }
}
