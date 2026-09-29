<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;

class AiComplianceAlert extends Model
{
    use HasOwnership;

    protected $fillable = [
        'type',
        'entity_type',
        'entity_id',
        'description',
        'status',
        'resolved_at',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function resolve(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
