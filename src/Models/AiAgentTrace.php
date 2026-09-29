<?php

namespace Agentoom\Compliance\Models;

use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\AgentConversation;
use Agentoom\Core\Models\AgentConversationMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAgentTrace extends Model
{
    protected $fillable = [
        'conversation_id',
        'agent_id',
        'ai_message_id',
        'model',
        'total_steps',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'execution_time_ms',
        'steps_json',
        'traceable_type',
        'traceable_id',
    ];

    protected $casts = [
        'steps_json' => 'array',
    ];

    /**
     * Get the conversation this trace belongs to.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AgentConversation::class, 'conversation_id');
    }

    /**
     * Get the specific message turn this trace belongs to.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(AgentConversationMessage::class, 'ai_message_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function traceable()
    {
        return $this->morphTo();
    }
}
