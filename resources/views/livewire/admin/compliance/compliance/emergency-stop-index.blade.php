<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-core::admin.compliance.emergency_stops') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-core::admin.compliance.emergency_stops_subtitle') }}</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:card class="max-w-xl">
        <flux:heading size="lg" class="mb-4">{{ __('agentoom-core::admin.compliance.stop_activate_heading') }}</flux:heading>
        <form wire:submit="activate" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.stop_entity_type') }}</flux:label>
                <flux:select wire:model.live="entityType">
                    <flux:select.option value="agent">Agent</flux:select.option>
                    <flux:select.option value="pipeline">Pipeline</flux:select.option>
                    <flux:select.option value="endpoint">Endpoint</flux:select.option>
                </flux:select>
                <flux:error name="entityType" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.stop_entity_id') }}</flux:label>
                <flux:select wire:model="entityId" searchable>
                    <flux:select.option value="">{{ __('agentoom-core::admin.compliance.risk_select') }}</flux:select.option>
                    @foreach ($entityOptions as $option)
                        <flux:select.option value="{{ $option->id }}">{{ $option->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="entityId" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.stop_reason') }}</flux:label>
                <flux:textarea wire:model="reason" rows="2" placeholder="{{ __('agentoom-core::admin.compliance.stop_reason_placeholder') }}" />
                <flux:error name="reason" />
            </flux:field>
            <flux:button type="submit" variant="danger" icon="stop-circle">{{ __('agentoom-core::admin.compliance.stop_activate_btn') }}</flux:button>
        </form>
    </flux:card>

    <flux:card>
        <flux:heading size="lg" class="mb-4">{{ __('agentoom-core::admin.compliance.stop_history') }}</flux:heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_entity') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_status') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_activated_by') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_reason') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_activated_at') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_reactivated_by') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($stops as $stop)
                    <flux:table.row>
                        <flux:table.cell>{{ $stop->entity_type }} #{{ $stop->entity_id }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $stop->is_active ? 'red' : 'green' }}">
                                {{ $stop->is_active ? __('agentoom-core::admin.compliance.status_active') : __('agentoom-core::admin.compliance.status_lifted') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $stop->activated_by ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $stop->reason ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $stop->activated_at?->format('Y-m-d H:i') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $stop->reactivated_by ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($stop->is_active)
                                <flux:button size="sm" variant="primary" wire:click="lift({{ $stop->id }})" wire:confirm="{{ __('agentoom-core::admin.compliance.stop_lift_confirm') }}">{{ __('agentoom-core::admin.compliance.lift_stop') }}</flux:button>
                            @else
                                <span class="text-zinc-400 text-sm">{{ $stop->reactivated_at?->format('Y-m-d H:i') }}</span>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-zinc-500">{{ __('agentoom-core::admin.compliance.stop_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $stops->links() }}
        </div>
    </flux:card>
</div>
