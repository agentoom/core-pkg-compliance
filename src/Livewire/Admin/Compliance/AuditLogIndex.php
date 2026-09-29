<?php

namespace Agentoom\Compliance\Livewire\Admin\Compliance;

use Agentoom\Compliance\Models\AiAuditLog;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogIndex extends Component
{
    use WithPagination;

    public function render(): View
    {
        $logs = AiAuditLog::orderByDesc('created_at')->paginate(20);

        return view('agentoom-compliance::livewire.admin.compliance.audit-log-index', compact('logs'))->layout('agentoom-core::layouts.admin');
    }
}
