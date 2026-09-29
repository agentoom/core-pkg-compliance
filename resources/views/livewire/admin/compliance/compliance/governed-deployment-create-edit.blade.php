<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('admin.compliance.governed-deployments.index') }}" variant="ghost" icon="arrow-left" size="sm">{{ __('agentoom-core::admin.compliance.back') }}</flux:button>
        <flux:heading size="xl">{{ $this->deploymentId ? __('agentoom-core::admin.compliance.deployment_edit') : __('agentoom-core::admin.compliance.deployment_new') }}</flux:heading>
    </div>

    <div class="max-w-2xl space-y-4">
        <flux:field>
            <flux:label>{{ __('agentoom-core::admin.compliance.deployment_name') }}</flux:label>
            <flux:input wire:model="name" placeholder="{{ __('agentoom-core::admin.compliance.deployment_name_placeholder') }}" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('agentoom-core::admin.compliance.deployment_purpose') }}</flux:label>
            <flux:textarea wire:model="businessPurpose" placeholder="{{ __('agentoom-core::admin.compliance.deployment_purpose_placeholder') }}" rows="3" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('agentoom-core::admin.compliance.col_deployable') }}</flux:label>
            <flux:select wire:model.live="deployableType">
                <flux:select.option value="">{{ __('agentoom-core::admin.compliance.deployment_profile_none') }}</flux:select.option>
                @foreach ($deployableTypes as $class => $label)
                    <flux:select.option value="{{ $class }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        @if ($deployableType)
            <flux:field>
                <flux:label>{{ __('agentoom-core::admin.compliance.risk_assessable') }}</flux:label>
                <flux:select wire:model="deployableId" searchable>
                    <flux:select.option value="">{{ __('agentoom-core::admin.compliance.risk_select') }}</flux:select.option>
                    @foreach ($deployableOptions as $option)
                        <flux:select.option value="{{ $option->id }}">{{ $option->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif

        <flux:field>
            <flux:label>{{ __('agentoom-core::admin.compliance.deployment_profile') }}</flux:label>
            <flux:select wire:model="governanceProfileId">
                <flux:select.option value="">{{ __('agentoom-core::admin.compliance.deployment_profile_none') }}</flux:select.option>
                @foreach($profiles as $profile)
                    <flux:select.option value="{{ $profile->id }}">{{ $profile->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        @if($classificationResult)
            <div class="rounded-lg border border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 p-4 space-y-2">
                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">{{ __('agentoom-core::admin.compliance.wizard_result_level') }}</p>
                <p class="text-sm text-blue-700 dark:text-blue-400">Governance Level: <strong>{{ ucfirst($classificationResult['governance_level']) }}</strong></p>
                <p class="text-sm text-blue-700 dark:text-blue-400">Potentially Sensitive: <strong>{{ $classificationResult['potentially_sensitive'] ? 'Yes' : 'No' }}</strong></p>
                <p class="text-sm text-blue-700 dark:text-blue-400">{{ $classificationResult['reasoning'] }}</p>
            </div>
        @endif

        <div class="flex gap-3 pt-2">
            <flux:button wire:click="save" variant="primary">{{ __('agentoom-core::admin.compliance.save') }}</flux:button>
            <flux:button href="{{ route('admin.compliance.governed-deployments.index') }}" variant="ghost">{{ __('agentoom-core::admin.compliance.cancel') }}</flux:button>
        </div>
    </div>
</div>
