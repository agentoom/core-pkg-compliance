<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;

class AiComplianceReport extends Model
{
    use HasOwnership;

    protected $fillable = [
        'title',
        'frequency',
        'period_start',
        'period_end',
        'summary',
        'generated_by',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'summary' => 'array',
        'assigned_at' => 'datetime',
    ];
}
