<div class="space-y-6">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('admin.compliance.reports.index') }}" icon="arrow-left" variant="subtle" size="sm" wire:navigate>{{ __('agentoom-core::admin.compliance.back') }}</flux:button>
        <div>
            <flux:heading size="xl">{{ $report->title }}</flux:heading>
            <flux:subheading>{{ ucfirst($report->frequency) }} report · {{ $report->period_start->format('Y-m-d') }} → {{ $report->period_end->format('Y-m-d') }}</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <flux:card>
            <flux:heading size="lg" class="mb-3">{{ __('agentoom-core::admin.compliance.report_incidents') }}</flux:heading>
            <p class="text-sm text-zinc-500">Total</p>
            <p class="text-2xl font-bold">{{ $report->summary['incidents']['total'] ?? 0 }}</p>
            @if (!empty($report->summary['incidents']['by_severity']))
                <div class="mt-3 space-y-1">
                    @foreach ($report->summary['incidents']['by_severity'] as $severity => $count)
                        <div class="flex justify-between text-sm">
                            <span class="capitalize text-zinc-600 dark:text-zinc-400">{{ $severity }}</span>
                            <span class="font-medium">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-3">{{ __('agentoom-core::admin.compliance.report_risk_assessments') }}</flux:heading>
            <p class="text-sm text-zinc-500">{{ __('agentoom-core::admin.compliance.report_overdue_reviews') }}</p>
            <p class="text-2xl font-bold {{ ($report->summary['risk_assessments']['overdue_reviews'] ?? 0) > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                {{ $report->summary['risk_assessments']['overdue_reviews'] ?? 0 }}
            </p>
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-3">{{ __('agentoom-core::admin.compliance.audit_log') }}</flux:heading>
            <p class="text-sm text-zinc-500">{{ __('agentoom-core::admin.compliance.report_audit_entries') }}</p>
            <p class="text-2xl font-bold">{{ $report->summary['audit_activity']['log_entries'] ?? 0 }}</p>
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-3">{{ __('agentoom-core::admin.compliance.report_human_interventions') }}</flux:heading>
            @foreach (['approved' => 'green', 'rejected' => 'red', 'overridden' => 'yellow'] as $status => $color)
                @if (isset($report->summary['human_interventions'][$status]))
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize text-zinc-600 dark:text-zinc-400">{{ $status }}</span>
                        <flux:badge color="{{ $color }}">{{ $report->summary['human_interventions'][$status] }}</flux:badge>
                    </div>
                @endif
            @endforeach
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-3">{{ __('agentoom-core::admin.compliance.report_avg_eval_score') }}</flux:heading>
            <p class="text-sm text-zinc-500">{{ __('agentoom-core::admin.compliance.dashboard_avg_eval_score') }}</p>
            <p class="text-2xl font-bold text-cyan-600">
                {{ $report->summary['evaluation_scores']['average'] !== null ? $report->summary['evaluation_scores']['average'] : '—' }}
            </p>
        </flux:card>
    </div>

    <flux:card>
        <flux:heading size="lg" class="mb-3">{{ __('agentoom-core::admin.compliance.report_raw_json') }}</flux:heading>
        <pre class="text-xs font-mono bg-zinc-50 dark:bg-zinc-900 p-4 rounded overflow-x-auto">{{ json_encode($report->summary, JSON_PRETTY_PRINT) }}</pre>
    </flux:card>
</div>
