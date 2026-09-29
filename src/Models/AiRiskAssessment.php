<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiRiskAssessment extends Model
{
    use HasOwnership;

    /** @var string[] Valid risk levels */
    public const RISK_LEVELS = ['unknown', 'under_review', 'minimal', 'limited', 'potential_high_risk'];

    protected $fillable = [
        'assessable_type',
        'assessable_id',
        'risk_level',
        'use_case_description',
        'affected_persons',
        'mitigations',
        'last_reviewed_at',
        'reviewed_by',
        'review_interval_days',
        'next_review_due_at',
        'owner_type',
        'owner_id',
        'assigned_at',
    ];

    protected $casts = [
        'last_reviewed_at' => 'datetime',
        'next_review_due_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (AiRiskAssessment $assessment) {
            $versionCount = AiRiskAssessmentVersion::where('risk_assessment_id', $assessment->id)->count();

            AiRiskAssessmentVersion::create(['risk_assessment_id' => $assessment->id,
                'version_number' => $versionCount + 1,
                'risk_level' => $assessment->getOriginal('risk_level'),
                'use_case_description' => $assessment->getOriginal('use_case_description'),
                'affected_persons' => $assessment->getOriginal('affected_persons'),
                'mitigations' => $assessment->getOriginal('mitigations'),
                'reviewed_by' => $assessment->getOriginal('reviewed_by'),
            ]);
        });

        static::updated(function (AiRiskAssessment $assessment) {
            if ($assessment->review_interval_days && $assessment->last_reviewed_at) {
                $assessment->updateQuietly([
                    'next_review_due_at' => $assessment->last_reviewed_at->addDays($assessment->review_interval_days),
                ]);
            }
        });
    }

    public function isReviewOverdue(): bool
    {
        return $this->next_review_due_at !== null && $this->next_review_due_at->isPast();
    }

    public function assessable(): MorphTo
    {
        return $this->morphTo();
    }

    public function versions(): HasMany
    {
        return $this->hasMany(AiRiskAssessmentVersion::class, 'risk_assessment_id')->orderByDesc('version_number');
    }
}
