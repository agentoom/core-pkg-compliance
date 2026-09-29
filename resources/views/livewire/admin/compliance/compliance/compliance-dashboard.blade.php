<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-core::admin.compliance.dashboard') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-core::admin.compliance.dashboard_subtitle') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:select wire:model.live="period" class="w-36">
                <flux:select.option value="7">{{ __('agentoom-core::admin.compliance.dashboard_period_7') }}</flux:select.option>
                <flux:select.option value="30">{{ __('agentoom-core::admin.compliance.dashboard_period_30') }}</flux:select.option>
                <flux:select.option value="90">{{ __('agentoom-core::admin.compliance.dashboard_period_90') }}</flux:select.option>
                <flux:select.option value="custom">{{ __('agentoom-core::admin.compliance.dashboard_period_custom') }}</flux:select.option>
            </flux:select>
            @if ($period === 'custom')
                <flux:input wire:model.live="customFrom" type="date" class="w-36" />
                <flux:input wire:model.live="customTo" type="date" class="w-36" />
            @endif
        </div>
    </div>

    {{-- Summary widgets --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <flux:card>
            <flux:heading size="sm">{{ __('agentoom-core::admin.compliance.dashboard_open_alerts') }}</flux:heading>
            <p class="text-3xl font-bold {{ $openAlerts > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">{{ $openAlerts }}</p>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">{{ __('agentoom-core::admin.compliance.dashboard_active_stops') }}</flux:heading>
            <p class="text-3xl font-bold {{ $activeEmergencyStops > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">{{ $activeEmergencyStops }}</p>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">{{ __('agentoom-core::admin.compliance.dashboard_overdue_reviews') }}</flux:heading>
            <p class="text-3xl font-bold {{ $overdueReviews > 0 ? 'text-yellow-600' : 'text-green-600' }} mt-1">{{ $overdueReviews }}</p>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">{{ __('agentoom-core::admin.compliance.dashboard_avg_eval_score') }}</flux:heading>
            <p class="text-3xl font-bold text-cyan-600 mt-1">{{ $averageEvaluationScore !== null ? number_format($averageEvaluationScore, 1) : '—' }}</p>
        </flux:card>
    </div>

    {{-- Human oversight rates --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">{{ __('agentoom-core::admin.compliance.pending_decisions') }}</flux:heading>
        <div class="grid grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-zinc-500">{{ __('agentoom-core::admin.compliance.status_approved') }}</p>
                <p class="text-2xl font-bold text-green-600">{{ $approvalRate !== null ? $approvalRate . '%' : '—' }}</p>
            </div>
            <div>
                <p class="text-sm text-zinc-500">{{ __('agentoom-core::admin.compliance.status_rejected') }}</p>
                <p class="text-2xl font-bold text-red-600">{{ $rejectionRate !== null ? $rejectionRate . '%' : '—' }}</p>
            </div>
            <div>
                <p class="text-sm text-zinc-500">{{ __('agentoom-core::admin.compliance.status_overridden') }}</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $overrideRate !== null ? $overrideRate . '%' : '—' }}</p>
            </div>
        </div>
    </flux:card>

    {{-- Incidents by severity --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">{{ __('agentoom-core::admin.compliance.dashboard_incidents_by_severity') }}</flux:heading>
        @if ($incidentsBySeverity->isEmpty())
            <p class="text-zinc-500 text-sm">{{ __('agentoom-core::admin.compliance.dashboard_no_incidents') }}</p>
        @else
            <div class="flex flex-wrap gap-4">
                @foreach (['critical' => 'red', 'high' => 'orange', 'medium' => 'yellow', 'low' => 'green'] as $severity => $color)
                    @if ($incidentsBySeverity->has($severity))
                        <div class="flex items-center gap-2">
                            <flux:badge color="{{ $color }}">{{ ucfirst($severity) }}</flux:badge>
                            <span class="font-bold text-lg">{{ $incidentsBySeverity->get($severity) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </flux:card>

    {{-- Incidents over time --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">{{ __('agentoom-core::admin.compliance.dashboard_recent_incidents') }}</flux:heading>
        @if ($incidentsOverTime->isEmpty())
            <p class="text-zinc-500 text-sm">{{ __('agentoom-core::admin.compliance.dashboard_no_incidents') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="text-sm w-full">
                    <thead>
                        <tr class="text-left text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-2 pr-4">Date</th>
                            <th class="pb-2">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incidentsOverTime as $row)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="py-1 pr-4">{{ $row->date }}</td>
                                <td class="py-1">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:card>

    {{-- Emergency stop events --}}
    <flux:card>
        <flux:heading size="lg" class="mb-2">{{ __('agentoom-core::admin.compliance.dashboard_active_stops') }}</flux:heading>
        <p class="text-3xl font-bold {{ $emergencyStopEvents > 0 ? 'text-red-600' : 'text-zinc-400' }}">{{ $emergencyStopEvents }}</p>
    </flux:card>
</div>
