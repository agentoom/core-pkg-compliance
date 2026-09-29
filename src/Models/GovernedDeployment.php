<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GovernedDeployment extends Model
{
    use HasOwnership;

    protected $fillable = [
        'name',
        'business_purpose',
        'deployable_type',
        'deployable_id',
        'governance_profile_id',
        'classification_governance_level',
        'classification_potentially_sensitive',
        'classification_recommended_controls',
        'classification_reasoning',
        'assessment_history',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'classification_potentially_sensitive' => 'boolean',
        'classification_recommended_controls' => 'array',
        'assessment_history' => 'array',
        'assigned_at' => 'datetime',
    ];

    public function deployable(): MorphTo
    {
        return $this->morphTo();
    }

    public function governanceProfile(): BelongsTo
    {
        return $this->belongsTo(GovernanceProfile::class);
    }

    public function applyClassification(array $result): void
    {
        $history = $this->assessment_history ?? [];
        $history[] = array_merge($result, ['assessed_at' => now()->toIso8601String()]);

        $this->update([
            'classification_governance_level' => $result['governance_level'] ?? null,
            'classification_potentially_sensitive' => $result['potentially_sensitive'] ?? false,
            'classification_recommended_controls' => $result['recommended_controls'] ?? [],
            'classification_reasoning' => $result['reasoning'] ?? null,
            'assessment_history' => $history,
        ]);
    }
}
