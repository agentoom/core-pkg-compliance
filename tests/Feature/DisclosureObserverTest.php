<?php

namespace Agentoom\Compliance\Tests\Feature;

use Agentoom\Core\Models\Agent;
use Agentoom\Core\Models\AgentConversation;
use Agentoom\Core\Models\Configuration;
use Agentoom\Core\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class DisclosureObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_disclosed_at_is_set_when_ai_disclosure_enabled(): void
    {
        Configuration::setValue('ai_disclosure_enabled', true);

        $user = User::factory()->create();
        $agent = Agent::factory()->create();

        $conversation = AgentConversation::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'title' => 'Test',
        ]);

        $this->assertNotNull($conversation->disclosed_at);
    }

    public function test_disclosed_at_is_null_when_ai_disclosure_disabled(): void
    {
        Configuration::setValue('ai_disclosure_enabled', false);

        $user = User::factory()->create();
        $agent = Agent::factory()->create();

        $conversation = AgentConversation::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'title' => 'Test',
        ]);

        $this->assertNull($conversation->disclosed_at);
    }
}
