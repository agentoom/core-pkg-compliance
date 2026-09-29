<?php

namespace Agentoom\Compliance\Models;

use Illuminate\Database\Eloquent\Model;

class GovernanceRecommendation extends Model
{
    protected $fillable = [
        'trigger',
        'recommendation',
        'severity',
        'rationale',
    ];
}
