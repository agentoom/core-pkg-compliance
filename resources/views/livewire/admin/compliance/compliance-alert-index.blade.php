<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.alerts') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-compliance::admin.compliance.alerts_subtitle') }}</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:card>
        <div class="flex gap-3 mb-4">
            <flux:select wire:model.live="statusFilter" class="w-40">
                <flux:select.option value="">{{ __('agentoom-compliance::admin.compliance.all') }}</flux:select.option>
                <flux:select.option value="open">{{ __('agentoom-compliance::admin.compliance.status_open') }}</flux:select.option>
                <flux:select.option value="resolved">{{ __('agentoom-compliance::admin.compliance.status_resolved') }}</flux:select.option>
            </flux:select>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_type') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_entity') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_description') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_status') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_created') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($alerts as $alert)
                    <flux:table.row>
                        <flux:table.cell>{{ $alert->type }}</flux:table.cell>
                        <flux:table.cell>{{ $alert->entity_type ? class_basename($alert->entity_type) . ' #' . $alert->entity_id : '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $alert->description }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $alert->status === 'open' ? 'red' : 'green' }}">
                                {{ ucfirst($alert->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $alert->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($alert->status === 'open')
                                <flux:button size="sm" variant="subtle" wire:click="resolve({{ $alert->id }})" wire:confirm="{{ __('agentoom-compliance::admin.compliance.alert_resolve_confirm') }}">{{ __('agentoom-compliance::admin.compliance.resolve') }}</flux:button>
                            @else
                                <span class="text-zinc-400 text-sm">{{ __('agentoom-compliance::admin.compliance.alert_resolved_on', ['date' => $alert->resolved_at?->format('Y-m-d')]) }}</span>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500">{{ __('agentoom-compliance::admin.compliance.alert_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $alerts->links() }}
        </div>
    </flux:card>
</div>
