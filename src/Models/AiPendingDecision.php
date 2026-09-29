<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\User;
use Illuminate\Database\Eloquent\Model;

class AiPendingDecision extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'metadata',
        'custom_decision',
        'override_reason',
        'overridden_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'overridden_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function approve(User $user): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);
    }

    public function reject(User $user): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);
    }

    public function override(User $user, string $customDecision, string $reason): void
    {
        $this->update([
            'status' => 'overridden',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'custom_decision' => $customDecision,
            'override_reason' => $reason,
            'overridden_at' => now(),
        ]);
    }
}
