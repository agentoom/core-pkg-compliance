<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.audit_log') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-compliance::admin.compliance.audit_log_subtitle') }}</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_id') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_entity') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_event_type') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_actor') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_input_hash') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_output_hash') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_created_at') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($logs as $log)
                    <flux:table.row>
                        <flux:table.cell>{{ $log->id }}</flux:table.cell>
                        <flux:table.cell>{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</flux:table.cell>
                        <flux:table.cell>{{ $log->event_type }}</flux:table.cell>
                        <flux:table.cell>{{ $log->actor_type ? $log->actor_type . ' #' . $log->actor_id : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $log->input_hash ? substr($log->input_hash, 0, 12) . '…' : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $log->output_hash ? substr($log->output_hash, 0, 12) . '…' : '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $log->created_at->format('Y-m-d H:i:s') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-zinc-500">{{ __('agentoom-compliance::admin.compliance.no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </flux:card>
</div>
