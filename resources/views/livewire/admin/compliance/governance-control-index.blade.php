<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.control_library') }}</flux:heading>
            <p class="text-sm text-zinc-500 mt-1">{{ __('agentoom-compliance::admin.compliance.control_library_subtitle') }}</p>
        </div>
    </div>

    <div class="mb-4">
        <flux:select wire:model.live="framework" class="max-w-xs">
            <flux:select.option value="">{{ __('agentoom-compliance::admin.compliance.all_frameworks') }}</flux:select.option>
            @foreach($frameworks as $fw)
                <flux:select.option value="{{ $fw }}">{{ strtoupper(str_replace('_', ' ', $fw)) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    @forelse($controls as $frameworkName => $frameworkControls)
        <div class="mb-8">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-3">
                {{ strtoupper(str_replace('_', ' ', $frameworkName)) }}
            </h3>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_code') }}</flux:table.column>
                    <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_title') }}</flux:table.column>
                    <flux:table.column>{{ __('agentoom-compliance::admin.compliance.col_description') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($frameworkControls as $control)
                        <flux:table.row>
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc">{{ $control->code }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">{{ $control->title }}</flux:table.cell>
                            <flux:table.cell class="text-sm text-zinc-500">{{ $control->description }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @empty
        <p class="text-zinc-400 text-center py-8">{{ __('agentoom-compliance::admin.compliance.no_entries') }}</p>
    @endforelse
</div>
