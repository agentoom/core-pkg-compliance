<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ $profileId ? __('agentoom-compliance::admin.compliance.edit_governance_profile') : __('agentoom-compliance::admin.compliance.new_governance_profile') }}</flux:heading>
        <flux:subheading>{{ __('agentoom-compliance::admin.compliance.governance_profile_form_subtitle') }}</flux:subheading>
    </div>

    <flux:card class="space-y-4">
        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_name') }}</flux:label>
                <flux:input wire:model="name" placeholder="{{ __('agentoom-compliance::admin.compliance.profile_name_placeholder') }}" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_description') }}</flux:label>
                <flux:textarea wire:model="description" rows="3" placeholder="{{ __('agentoom-compliance::admin.compliance.profile_description_placeholder') }}" />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_governance_level') }}</flux:label>
                <flux:select wire:model="governance_level">
                    @foreach ($governanceLevels as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="governance_level" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_controls') }}</flux:label>
                <div class="grid grid-cols-2 gap-2 mt-1">
                    @foreach ($availableControls as $value => $label)
                        <flux:checkbox wire:model="controls" value="{{ $value }}" label="{{ $label }}" />
                    @endforeach
                </div>
                <flux:error name="controls" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_status') }}</flux:label>
                <flux:switch wire:model="active" label="{{ __('agentoom-compliance::admin.compliance.status_active') }}" />
            </flux:field>

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary">{{ __('agentoom-compliance::admin.compliance.save') }}</flux:button>
                <flux:button href="{{ route('admin.compliance.governance-profiles.index') }}" variant="subtle" wire:navigate>{{ __('agentoom-compliance::admin.compliance.cancel') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
