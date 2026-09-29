<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\AgentConversation;
use Agentoom\Core\Models\Configuration;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class DisclosureEvidenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_disclosure_evidence_stored_when_enabled(): void
    {
        Configuration::setValue('ai_disclosure_enabled', '1');
        Configuration::setValue('ai_disclosure_text', 'You are interacting with an AI assistant.');
        Configuration::setValue('ai_disclosure_version', 'v1.0');

        $user = User::factory()->create();
        $agent = Agent::factory()->create();

        $conversation = AgentConversation::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'title' => 'Test',
        ]);

        $this->assertNotNull($conversation->disclosed_at);
        $this->assertEquals('You are interacting with an AI assistant.', $conversation->disclosure_text);
        $this->assertEquals('v1.0', $conversation->disclosure_version);
        $this->assertNotNull($conversation->disclosure_language);
    }

    public function test_disclosure_evidence_not_stored_when_disabled(): void
    {
        Configuration::setValue('ai_disclosure_enabled', '0');

        $user = User::factory()->create();
        $agent = Agent::factory()->create();

        $conversation = AgentConversation::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'title' => 'Test',
        ]);

        $this->assertNull($conversation->disclosed_at);
        $this->assertNull($conversation->disclosure_text);
        $this->assertNull($conversation->disclosure_version);
    }
}
