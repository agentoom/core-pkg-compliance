<div class="space-y-6">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('admin.compliance.incidents.index') }}" icon="arrow-left" variant="subtle" size="sm" wire:navigate>{{ __('agentoom-core::admin.compliance.back') }}</flux:button>
        <div>
            <flux:heading size="xl">{{ $incidentId ? __('agentoom-core::admin.compliance.incident_edit') : __('agentoom-core::admin.compliance.incident_new') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-core::admin.compliance.incident_art') }}</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.incident_severity') }}</flux:label>
                <flux:select wire:model="severity">
                    <flux:select.option value="low">{{ __('agentoom-core::admin.compliance.severity_low') }}</flux:select.option>
                    <flux:select.option value="medium">{{ __('agentoom-core::admin.compliance.severity_medium') }}</flux:select.option>
                    <flux:select.option value="high">{{ __('agentoom-core::admin.compliance.severity_high') }}</flux:select.option>
                    <flux:select.option value="critical">{{ __('agentoom-core::admin.compliance.severity_critical') }}</flux:select.option>
                </flux:select>
                <flux:error name="severity" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.incident_description') }}</flux:label>
                <flux:textarea wire:model="description" rows="4" placeholder="{{ __('agentoom-core::admin.compliance.incident_description_placeholder') }}" />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.incident_status') }}</flux:label>
                <flux:select wire:model="status">
                    <flux:select.option value="open">{{ __('agentoom-core::admin.compliance.status_open') }}</flux:select.option>
                    <flux:select.option value="investigating">{{ __('agentoom-core::admin.compliance.status_investigating') }}</flux:select.option>
                    <flux:select.option value="resolved">{{ __('agentoom-core::admin.compliance.status_resolved') }}</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.incident_resolved_at') }}</flux:label>
                <flux:input type="datetime-local" wire:model="resolved_at" />
                <flux:error name="resolved_at" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.incident_notified_authority_at') }}</flux:label>
                <flux:input type="datetime-local" wire:model="notified_authority_at" />
                <flux:error name="notified_authority_at" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button href="{{ route('admin.compliance.incidents.index') }}" variant="subtle" wire:navigate>{{ __('agentoom-core::admin.compliance.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ $incidentId ? __('agentoom-core::admin.compliance.update') : __('agentoom-core::admin.compliance.create') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
