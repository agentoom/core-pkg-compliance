<div class="space-y-6">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('admin.compliance.audit-log.index') }}" icon="arrow-left" variant="subtle" size="sm" wire:navigate>{{ __('agentoom-compliance::admin.compliance.trace_back') }}</flux:button>
        <div>
            <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.trace_heading') }}</flux:heading>
            <flux:subheading>Trace ID: <span class="font-mono">{{ $traceId }}</span></flux:subheading>
        </div>
    </div>

    <flux:card>
        @forelse ($entries as $index => $entry)
            <div class="relative pl-8 pb-6 {{ !$loop->last ? 'border-l-2 border-zinc-200 dark:border-zinc-700 ml-3' : '' }}">
                <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-cyan-100 dark:bg-cyan-900 flex items-center justify-center text-xs font-bold text-cyan-700 dark:text-cyan-300">
                    {{ $index + 1 }}
                </div>
                <div class="ml-4">
                    <div class="flex items-center gap-3 mb-1">
                        <flux:badge color="{{ match($entry->event_type) { 'tool_call' => 'yellow', 'pipeline_run' => 'blue', 'endpoint_call' => 'purple', default => 'zinc' } }}">
                            {{ $entry->event_type }}
                        </flux:badge>
                        <span class="text-xs text-zinc-500">{{ $entry->created_at->format('Y-m-d H:i:s.u') }}</span>
                    </div>
                    <div class="text-sm text-zinc-700 dark:text-zinc-300 space-y-1">
                        <div><span class="font-medium">Entity:</span> {{ class_basename($entry->entity_type) }} #{{ $entry->entity_id }}</div>
                        @if ($entry->actor_type)
                            <div><span class="font-medium">Actor:</span> {{ $entry->actor_type }} #{{ $entry->actor_id }}</div>
                        @endif
                        @if ($entry->tool_name)
                            <div><span class="font-medium">Tool:</span> {{ $entry->tool_name }}</div>
                        @endif
                        @if ($entry->model_name)
                            <div><span class="font-medium">Model:</span> {{ $entry->model_provider ? $entry->model_provider . ' / ' : '' }}{{ $entry->model_name }}{{ $entry->model_version ? ' (' . $entry->model_version . ')' : '' }}</div>
                        @endif
                        @if ($entry->execution_duration_ms !== null)
                            <div><span class="font-medium">Duration:</span> {{ $entry->execution_duration_ms }} ms</div>
                        @endif
                        <div class="font-mono text-xs text-zinc-400 mt-1">
                            <span>in: {{ $entry->input_hash ? substr($entry->input_hash, 0, 16) . '…' : '—' }}</span>
                            <span class="ml-4">out: {{ $entry->output_hash ? substr($entry->output_hash, 0, 16) . '…' : '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-zinc-500 text-sm">{{ __('agentoom-compliance::admin.compliance.trace_no_entries') }}</p>
        @endforelse
    </flux:card>
</div>
