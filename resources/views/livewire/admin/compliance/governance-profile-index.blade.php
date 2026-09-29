<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.governance_profiles') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-compliance::admin.compliance.governance_profiles_subtitle') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.compliance.governance-profiles.create') }}" icon="plus" variant="primary" size="sm" wire:navigate>
            {{ __('agentoom-compliance::admin.compliance.new_governance_profile') }}
        </flux:button>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="x-circle">{{ session('error') }}</flux:callout>
    @endif

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_name') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_governance_level') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_controls') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_deployments') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_status') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($profiles as $profile)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-medium">{{ $profile->name }}</div>
                            @if ($profile->description)
                                <div class="text-sm text-zinc-500">{{ $profile->description }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $profile->governance_level === 'enhanced' ? 'red' : ($profile->governance_level === 'standard' ? 'yellow' : 'green') }}">
                                {{ ucfirst($profile->governance_level) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($profile->controls ?? [] as $control)
                                    <flux:badge size="sm" color="zinc">{{ str_replace('_', ' ', $control) }}</flux:badge>
                                @endforeach
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $profile->governed_deployments_count ?? $profile->governedDeployments()->count() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $profile->active ? 'green' : 'zinc' }}">
                                {{ $profile->active ? __('agentoom-compliance::admin.compliance.status_active') : __('agentoom-compliance::admin.compliance.status_inactive') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="subtle" href="{{ route('admin.compliance.governance-profiles.edit', $profile->id) }}" wire:navigate>{{ __('agentoom-compliance::admin.compliance.edit') }}</flux:button>
                                <flux:button size="sm" variant="subtle" wire:click="toggleActive({{ $profile->id }})">
                                    {{ $profile->active ? __('agentoom-compliance::admin.compliance.deactivate') : __('agentoom-compliance::admin.compliance.activate') }}
                                </flux:button>
                                <flux:button size="sm" variant="danger" wire:click="delete({{ $profile->id }})" wire:confirm="{{ __('agentoom-compliance::admin.compliance.confirm_delete') }}">{{ __('agentoom-compliance::admin.compliance.delete') }}</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500">{{ __('agentoom-compliance::admin.compliance.profile_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $profiles->links() }}
        </div>
    </flux:card>
</div>
