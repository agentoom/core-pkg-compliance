<?php

namespace Agentoom\Compliance\Models;

use Illuminate\Database\Eloquent\Model;

class AiEmergencyStop extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'is_active',
        'activated_by',
        'reason',
        'activated_at',
        'reactivated_by',
        'reactivated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'reactivated_at' => 'datetime',
    ];

    /**
     * Check whether a given entity currently has an active emergency stop.
     */
    public static function isStoppedFor(string $entityType, int|string $entityId): bool
    {
        return static::where('entity_type', strtolower($entityType))
            ->where('entity_id', $entityId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Activate an emergency stop for an entity.
     */
    public static function activateFor(string $entityType, int|string $entityId, string $activatedBy, ?string $reason = null): static
    {
        return static::create([
            'entity_type' => strtolower($entityType),
            'entity_id' => $entityId,
            'is_active' => true,
            'activated_by' => $activatedBy,
            'reason' => $reason,
            'activated_at' => now(),
        ]);
    }

    /**
     * Reactivate (lift the stop) for an entity.
     */
    public function lift(string $reactivatedBy): void
    {
        $this->update([
            'is_active' => false,
            'reactivated_by' => $reactivatedBy,
            'reactivated_at' => now(),
        ]);
    }
}
