<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EvidencePackage extends Model
{
    use HasOwnership;

    protected $fillable = [
        'name',
        'subject_type',
        'subject_id',
        'format',
        'status',
        'storage_path',
        'included_sections',
        'generated_by',
        'generated_at',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'included_sections' => 'array',
        'generated_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function markGenerating(): void
    {
        $this->update(['status' => 'generating']);
    }

    public function markReady(string $storagePath): void
    {
        $this->update([
            'status' => 'ready',
            'storage_path' => $storagePath,
            'generated_at' => now(),
        ]);
    }

    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
