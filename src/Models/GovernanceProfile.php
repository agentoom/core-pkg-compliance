<?php

namespace Agentoom\Compliance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GovernanceProfile extends Model
{
    protected $fillable = [
        'name',
        'description',
        'governance_level',
        'controls',
        'active',
    ];

    protected $casts = [
        'controls' => 'array',
        'active' => 'boolean',
    ];

    public function governedDeployments(): HasMany
    {
        return $this->hasMany(GovernedDeployment::class);
    }

    public function complianceAssessments(): HasMany
    {
        return $this->hasMany(ComplianceAssessment::class);
    }
}
