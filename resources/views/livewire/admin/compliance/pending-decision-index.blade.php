<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.pending_decisions') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-compliance::admin.compliance.pending_decisions_subtitle') }}</flux:subheading>
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
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_description') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_created_at') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($decisions as $decision)
                    <flux:table.row>
                        <flux:table.cell>{{ $decision->id }}</flux:table.cell>
                        <flux:table.cell>{{ class_basename($decision->entity_type) }} #{{ $decision->entity_id }}</flux:table.cell>
                        <flux:table.cell>{{ $decision->description ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $decision->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="primary" wire:click="approve({{ $decision->id }})" wire:confirm="{{ __('agentoom-compliance::admin.compliance.decision_approve_confirm') }}">{{ __('agentoom-compliance::admin.compliance.approve') }}</flux:button>
                                <flux:button size="sm" variant="danger" wire:click="reject({{ $decision->id }})" wire:confirm="{{ __('agentoom-compliance::admin.compliance.decision_reject_confirm') }}">{{ __('agentoom-compliance::admin.compliance.reject') }}</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center text-zinc-500">{{ __('agentoom-compliance::admin.compliance.decision_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $decisions->links() }}
        </div>
    </flux:card>
</div>
