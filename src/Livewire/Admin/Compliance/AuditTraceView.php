<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiAuditLog;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AuditTraceView extends Component
{
    public string $traceId = '';

    public function mount(string $traceId): void
    {
        $this->traceId = $traceId;
    }

    public function render(): View
    {
        $entries = AiAuditLog::where('trace_id', $this->traceId)
            ->orderBy('created_at')
            ->get();

        return view('agentoom-compliance::livewire.admin.compliance.audit-trace-view', compact('entries'))
            ->layout('agentoom-core::layouts.admin');
    }
}
