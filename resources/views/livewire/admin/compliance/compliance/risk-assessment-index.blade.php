<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-core::admin.compliance.risk_assessments') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-core::admin.compliance.risk_assessments_subtitle') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.compliance.risk-assessments.create-edit') }}" icon="plus" variant="primary" size="sm" wire:navigate>
            {{ __('agentoom-core::admin.compliance.new_assessment') }}
        </flux:button>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_id') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_assessable') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_risk_level') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_reviewed_by') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_last_reviewed') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($assessments as $assessment)
                    <flux:table.row>
                        <flux:table.cell>{{ $assessment->id }}</flux:table.cell>
                        <flux:table.cell>{{ class_basename($assessment->assessable_type) }} #{{ $assessment->assessable_id }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ match($assessment->risk_level) { 'high' => 'red', 'limited' => 'yellow', default => 'green' } }}">
                                {{ ucfirst($assessment->risk_level) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $assessment->reviewed_by ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $assessment->last_reviewed_at?->format('Y-m-d') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="subtle" href="{{ route('admin.compliance.risk-assessments.create-edit', ['assessmentId' => $assessment->id]) }}" wire:navigate>{{ __('agentoom-core::admin.compliance.edit') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500">{{ __('agentoom-core::admin.compliance.risk_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $assessments->links() }}
        </div>
    </flux:card>
</div>
