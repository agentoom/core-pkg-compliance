<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceAssessment extends Model
{
    use HasOwnership;

    protected $fillable = [
        'agent_id',
        'name',
        'ai_purpose',
        'ai_actions',
        'affected_groups',
        'human_review_frequency',
        'data_processed',
        'governance_level',
        'potentially_sensitive',
        'recommended_controls',
        'reasoning',
        'governance_profile_id',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'ai_actions' => 'array',
        'affected_groups' => 'array',
        'data_processed' => 'array',
        'recommended_controls' => 'array',
        'potentially_sensitive' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function governanceProfile(): BelongsTo
    {
        return $this->belongsTo(GovernanceProfile::class);
    }
}
