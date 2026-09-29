<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-core::admin.compliance.evaluations') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-core::admin.compliance.evaluations_subtitle') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.compliance.evaluations.create-edit') }}" icon="plus" variant="primary" size="sm" wire:navigate>
            {{ __('agentoom-core::admin.compliance.new_evaluation') }}
        </flux:button>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    @if ($averageScore !== null)
        <flux:card class="max-w-xs">
            <flux:heading size="sm">{{ __('agentoom-core::admin.compliance.dashboard_avg_eval_score') }}</flux:heading>
            <p class="text-3xl font-bold text-cyan-600 mt-1">{{ number_format($averageScore, 2) }}</p>
        </flux:card>
    @endif

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_entity') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_dataset') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_metric') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_score') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_reviewed_by') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_created_at') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($evaluations as $evaluation)
                    <flux:table.row>
                        <flux:table.cell>{{ class_basename($evaluation->entity_type) }} #{{ $evaluation->entity_id }}</flux:table.cell>
                        <flux:table.cell>{{ $evaluation->dataset_name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $evaluation->metric }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($evaluation->score !== null)
                                <flux:badge color="{{ $evaluation->score >= 70 ? 'green' : ($evaluation->score >= 40 ? 'yellow' : 'red') }}">
                                    {{ number_format($evaluation->score, 2) }}
                                </flux:badge>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $evaluation->evaluated_by ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $evaluation->created_at->format('Y-m-d') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="subtle" href="{{ route('admin.compliance.evaluations.edit', $evaluation->id) }}" wire:navigate>{{ __('agentoom-core::admin.compliance.edit') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-zinc-500">{{ __('agentoom-core::admin.compliance.eval_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $evaluations->links() }}
        </div>
    </flux:card>
</div>
