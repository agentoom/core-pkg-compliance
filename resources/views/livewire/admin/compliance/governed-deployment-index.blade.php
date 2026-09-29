<div>
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.governed_deployments') }}</flux:heading>
        <flux:button href="{{ route('admin.compliance.governed-deployments.create') }}" variant="primary" icon="plus">{{ __('agentoom-compliance::admin.compliance.new_deployment') }}</flux:button>
    </div>

    <div class="mb-4">
        <flux:input wire:model.live="search" placeholder="Search deployments..." icon="magnifying-glass" />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_name') }}</flux:table.column>
            <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_deployable') }}</flux:table.column>
            <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_profile') }}</flux:table.column>
            <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_governance_level') }}</flux:table.column>
            <flux:table.column>{{ __('agentoom-compliance::admin.compliance.wizard_sensitive_flag') }}</flux:table.column>
            <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_created') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($deployments as $deployment)
                <flux:table.row>
                    <flux:table.cell>{{ $deployment->name }}</flux:table.cell>
                    <flux:table.cell>
                        @if($deployment->deployable_type)
                            <span class="text-xs text-zinc-500">{{ class_basename($deployment->deployable_type) }} #{{ $deployment->deployable_id }}</span>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $deployment->governanceProfile?->name ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        @if($deployment->classification_governance_level)
                            @php $level = $deployment->classification_governance_level; @endphp
                            <flux:badge color="{{ $level === 'enhanced' ? 'red' : ($level === 'standard' ? 'yellow' : 'green') }}" size="sm">
                                {{ ucfirst($level) }}
                            </flux:badge>
                        @else
                            <span class="text-zinc-400">{{ __('agentoom-compliance::admin.compliance.deployment_unclassified') }}</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($deployment->classification_potentially_sensitive)
                            <flux:badge color="orange" size="sm">Yes</flux:badge>
                        @else
                            <span class="text-zinc-400">No</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $deployment->created_at->diffForHumans() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button href="{{ route('admin.compliance.governed-deployments.edit', $deployment->id) }}" size="sm" variant="ghost" icon="pencil">{{ __('agentoom-compliance::admin.compliance.edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-zinc-400 py-8">{{ __('agentoom-compliance::admin.compliance.deployment_no_entries') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $deployments->links() }}</div>
</div>
