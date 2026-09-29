<div class="space-y-6">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('admin.compliance.risk-assessments.index') }}" icon="arrow-left" variant="subtle" size="sm" wire:navigate>{{ __('agentoom-compliance::admin.compliance.back') }}</flux:button>
        <div>
            <flux:heading size="xl">{{ $assessmentId ? __('agentoom-compliance::admin.compliance.risk_edit') : __('agentoom-compliance::admin.compliance.risk_new') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-compliance::admin.compliance.risk_art') }}</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.risk_assessable_type') }}</flux:label>
                <flux:select wire:model.live="assessable_type">
                    @foreach ($assessableTypes as $class => $label)
                        <flux:select.option value="{{ $class }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="assessable_type" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.risk_assessable') }}</flux:label>
                <flux:select wire:model="assessable_id" searchable>
                    <flux:select.option value="">{{ __('agentoom-compliance::admin.compliance.risk_select') }}</flux:select.option>
                    @foreach ($assessableOptions as $option)
                        <flux:select.option value="{{ $option->id }}">{{ $option->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="assessable_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_risk_level') }}</flux:label>
                <flux:select wire:model="risk_level">
                    <flux:select.option value="unknown">{{ __('agentoom-compliance::admin.compliance.risk_unknown') }}</flux:select.option>
                    <flux:select.option value="under_review">{{ __('agentoom-compliance::admin.compliance.risk_under_review') }}</flux:select.option>
                    <flux:select.option value="minimal">{{ __('agentoom-compliance::admin.compliance.risk_minimal') }}</flux:select.option>
                    <flux:select.option value="limited">{{ __('agentoom-compliance::admin.compliance.risk_limited') }}</flux:select.option>
                    <flux:select.option value="potential_high_risk">{{ __('agentoom-compliance::admin.compliance.risk_potential_high_risk') }}</flux:select.option>
                </flux:select>
                <flux:error name="risk_level" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.risk_use_case') }}</flux:label>
                <flux:textarea wire:model="use_case_description" rows="3" />
                <flux:error name="use_case_description" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.risk_affected_persons') }}</flux:label>
                <flux:textarea wire:model="affected_persons" rows="2" />
                <flux:error name="affected_persons" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.risk_mitigations') }}</flux:label>
                <flux:textarea wire:model="mitigations" rows="3" />
                <flux:error name="mitigations" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.risk_last_reviewed_at') }}</flux:label>
                <flux:input type="date" wire:model="last_reviewed_at" />
                <flux:error name="last_reviewed_at" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.risk_reviewed_by') }}</flux:label>
                <flux:input type="text" wire:model="reviewed_by" placeholder="{{ __('agentoom-compliance::admin.compliance.risk_reviewed_by_placeholder') }}" />
                <flux:error name="reviewed_by" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button href="{{ route('admin.compliance.risk-assessments.index') }}" variant="subtle" wire:navigate>{{ __('agentoom-compliance::admin.compliance.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ $assessmentId ? __('agentoom-compliance::admin.compliance.update') : __('agentoom-compliance::admin.compliance.create') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
