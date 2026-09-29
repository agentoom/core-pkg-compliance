<?php

namespace Agentoom\Compliance\Livewire\Admin;

use Agentoom\Compliance\Models\AiAgentTrace;
use Agentoom\Core\Models\Agent;
use Livewire\Component;
use Livewire\WithPagination;

class AgentTraceIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $agentId = '';

    public $conversationId = '';

    public $selectedConversationId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'agentId' => ['except' => ''],
        'conversationId' => ['except' => ''],
        'selectedConversationId' => ['except' => null],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedAgentId(): void
    {
        $this->resetPage();
    }

    public function updatedConversationId(): void
    {
        $this->resetPage();
    }

    public function selectConversation($id): void
    {
        $this->selectedConversationId = $id;
        $this->resetPage();
    }

    public function clearConversation(): void
    {
        $this->selectedConversationId = null;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->agentId = '';
        $this->conversationId = '';
        $this->resetPage();
    }

    public function render()
    {
        if ($this->selectedConversationId) {
            $query = AiAgentTrace::query()
                ->where('conversation_id', $this->selectedConversationId)
                ->with(['agent', 'conversation', 'message'])
                ->latest();

            $traces = $query->orderBy('created_at', 'desc')->paginate(20);

            return view('agentoom-compliance::livewire.admin.agent-trace-index', [
                'traces' => $traces,
                'agents' => Agent::orderBy('name')->get(),
            ])->layout('agentoom-core::layouts.admin');
        }

        $latestTraceIds = AiAgentTrace::query()
            ->selectRaw('MAX(id)')
            ->groupBy('conversation_id');

        $query = AiAgentTrace::query()
            ->whereIn('id', $latestTraceIds)
            ->with(['agent', 'conversation' => fn ($q) => $q->withCount(['messages', 'traces'])->withMax('messages', 'created_at')])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('model', 'like', '%'.$this->search.'%')
                    ->orWhereHas('agent', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    })
                    ->orWhere('conversation_id', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->agentId) {
            $query->where('agent_id', $this->agentId);
        }

        if ($this->conversationId) {
            $query->where('conversation_id', $this->conversationId);
        }

        $conversations = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('agentoom-compliance::livewire.admin.agent-trace-index', [
            'conversations' => $conversations,
            'agents' => Agent::orderBy('name')->get(),
        ])->layout('agentoom-core::layouts.admin');
    }
}
