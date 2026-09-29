<?php

namespace Agentoom\Compliance\Models;

use Illuminate\Database\Eloquent\Model;

class GovernanceControl extends Model
{
    protected $fillable = [
        'framework',
        'code',
        'title',
        'description',
    ];
}
