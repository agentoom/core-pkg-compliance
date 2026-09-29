<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;

class AiEvaluation extends Model
{
    use HasOwnership;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'dataset_name',
        'score',
        'metric',
        'notes',
        'evaluated_by',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'score' => 'float',
        'assigned_at' => 'datetime',
    ];
}
