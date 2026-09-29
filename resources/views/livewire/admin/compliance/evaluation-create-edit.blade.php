<div class="space-y-6">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('admin.compliance.evaluations.index') }}" icon="arrow-left" variant="subtle" size="sm" wire:navigate>{{ __('agentoom-compliance::admin.compliance.back') }}</flux:button>
        <div>
            <flux:heading size="xl">{{ $evaluationId ? __('agentoom-compliance::admin.compliance.eval_edit') : __('agentoom-compliance::admin.compliance.eval_new') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-compliance::admin.compliance.eval_art') }}</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.eval_entity_type') }}</flux:label>
                <flux:select wire:model.live="entity_type">
                    @foreach ($assessableTypes as $class => $label)
                        <flux:select.option value="{{ $class }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="entity_type" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_entity') }}</flux:label>
                <flux:select wire:model="entity_id" searchable>
                    <flux:select.option value="">{{ __('agentoom-compliance::admin.compliance.risk_select') }}</flux:select.option>
                    @foreach ($entityOptions as $option)
                        <flux:select.option value="{{ $option['id'] }}">{{ $option['name'] }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="entity_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.eval_metric') }}</flux:label>
                <flux:input wire:model="metric" type="text" placeholder="{{ __('agentoom-compliance::admin.compliance.eval_metric_placeholder') }}" />
                <flux:error name="metric" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.eval_score') }}</flux:label>
                <flux:input wire:model="score" type="number" step="0.01" min="0" max="100" placeholder="Optional numeric score" />
                <flux:error name="score" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.eval_dataset') }}</flux:label>
                <flux:input wire:model="dataset_name" type="text" placeholder="Optional dataset identifier" />
                <flux:error name="dataset_name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.eval_notes') }}</flux:label>
                <flux:textarea wire:model="notes" rows="3" placeholder="Observations, methodology, caveats" />
                <flux:error name="notes" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('agentoom-compliance::admin.compliance.col_reviewed_by') }}</flux:label>
                <flux:input wire:model="evaluated_by" type="text" placeholder="Name or user ID" />
                <flux:error name="evaluated_by" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button href="{{ route('admin.compliance.evaluations.index') }}" variant="subtle" wire:navigate>{{ __('agentoom-compliance::admin.compliance.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ $evaluationId ? __('agentoom-compliance::admin.compliance.update') : __('agentoom-compliance::admin.compliance.create') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
