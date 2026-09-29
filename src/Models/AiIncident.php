<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;

class AiIncident extends Model
{
    use HasOwnership;

    protected $fillable = [
        'severity',
        'description',
        'affected_entity_type',
        'affected_entity_id',
        'reported_by',
        'status',
        'resolved_at',
        'notified_authority_at',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'notified_authority_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];
}
