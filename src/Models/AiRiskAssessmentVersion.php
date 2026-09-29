<?php

namespace Agentoom\Compliance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRiskAssessmentVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'risk_assessment_id',
        'version_number',
        'risk_level',
        'use_case_description',
        'affected_persons',
        'mitigations',
        'reviewed_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function riskAssessment(): BelongsTo
    {
        return $this->belongsTo(AiRiskAssessment::class);
    }
}
