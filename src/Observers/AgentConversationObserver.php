<?php

namespace Agentoom\Compliance\Observers;

use Agentoom\Core\Models\AgentConversation;
use Agentoom\Core\Models\Configuration;

class AgentConversationObserver
{
    public function creating(AgentConversation $conversation): void
    {
        if (Configuration::getValue('ai_disclosure_enabled')) {
            $conversation->disclosed_at = now();
            $conversation->disclosure_text = Configuration::getValue('ai_disclosure_text') ?: null;
            $conversation->disclosure_version = Configuration::getValue('ai_disclosure_version') ?: null;
            $conversation->disclosure_language = app()->getLocale();
        }
    }
}
