<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-core::admin.compliance.reports') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-core::admin.compliance.reports_subtitle') }}</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:card class="max-w-sm">
        <flux:heading size="lg" class="mb-3">{{ __('agentoom-core::admin.compliance.report_generate_heading') }}</flux:heading>
        <div class="flex gap-3 items-end">
            <flux:field class="flex-1">
                <flux:label>{{ __('agentoom-core::admin.compliance.report_frequency') }}</flux:label>
                <flux:select wire:model="frequency">
                    <flux:select.option value="weekly">{{ __('agentoom-core::admin.compliance.report_frequency_weekly') }}</flux:select.option>
                    <flux:select.option value="monthly">{{ __('agentoom-core::admin.compliance.report_frequency_monthly') }}</flux:select.option>
                    <flux:select.option value="quarterly">{{ __('agentoom-core::admin.compliance.report_frequency_quarterly') }}</flux:select.option>
                </flux:select>
            </flux:field>
            <flux:button wire:click="generate" variant="primary">{{ __('agentoom-core::admin.compliance.report_generate_btn') }}</flux:button>
        </div>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_name') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_frequency') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_created') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_reviewed_by') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_generated_at') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($reports as $report)
                    <flux:table.row>
                        <flux:table.cell>{{ $report->title }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($report->frequency) }}</flux:table.cell>
                        <flux:table.cell>{{ $report->period_start->format('Y-m-d') }} → {{ $report->period_end->format('Y-m-d') }}</flux:table.cell>
                        <flux:table.cell>{{ $report->generated_by ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $report->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="subtle" href="{{ route('admin.compliance.reports.show', $report->id) }}" wire:navigate>{{ __('agentoom-core::admin.compliance.report_view') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500">{{ __('agentoom-core::admin.compliance.report_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </flux:card>
</div>
