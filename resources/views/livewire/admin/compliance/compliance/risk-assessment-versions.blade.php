<div class="space-y-6">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('admin.compliance.risk-assessments.index') }}" icon="arrow-left" variant="subtle" size="sm" wire:navigate>{{ __('agentoom-core::admin.compliance.back') }}</flux:button>
        <div>
            <flux:heading size="xl">{{ __('agentoom-core::admin.compliance.risk_assessment_versions') }}</flux:heading>
            <flux:subheading>Risk Assessment #{{ $assessment->id }} — {{ class_basename($assessment->assessable_type) }} #{{ $assessment->assessable_id }}</flux:subheading>
        </div>
    </div>

    <flux:card>
        <flux:heading size="lg" class="mb-2">{{ __('agentoom-core::admin.compliance.col_version') }}</flux:heading>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm mt-3">
            <div>
                <dt class="text-zinc-500">{{ __('agentoom-core::admin.compliance.col_risk_level') }}</dt>
                <dd>
                    <flux:badge color="{{ match($assessment->risk_level) { 'high' => 'red', 'limited' => 'yellow', default => 'green' } }}">
                        {{ ucfirst($assessment->risk_level) }}
                    </flux:badge>
                </dd>
            </div>
            <div>
                <dt class="text-zinc-500">{{ __('agentoom-core::admin.compliance.col_reviewed_by') }}</dt>
                <dd>{{ $assessment->reviewed_by ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-zinc-500">{{ __('agentoom-core::admin.compliance.col_last_reviewed') }}</dt>
                <dd>{{ $assessment->last_reviewed_at?->format('Y-m-d') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-zinc-500">{{ __('agentoom-core::admin.compliance.risk_last_reviewed_at') }}</dt>
                <dd class="{{ $assessment->isReviewOverdue() ? 'text-red-600 font-semibold' : '' }}">
                    {{ $assessment->next_review_due_at?->format('Y-m-d') ?? '—' }}
                    @if ($assessment->isReviewOverdue())
                        <flux:badge color="red" class="ml-1">{{ __('agentoom-core::admin.compliance.risk_overdue_badge') }}</flux:badge>
                    @endif
                </dd>
            </div>
        </dl>
    </flux:card>

    <flux:card>
        <flux:heading size="lg" class="mb-4">{{ __('agentoom-core::admin.compliance.risk_assessment_versions') }}</flux:heading>
        @forelse ($assessment->versions as $version)
            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-4 last:border-0 last:mb-0 last:pb-0">
                <div class="flex items-center gap-3 mb-2">
                    <flux:badge color="zinc">v{{ $version->version_number }}</flux:badge>
                    <span class="text-xs text-zinc-500">{{ $version->created_at->format('Y-m-d H:i') }}</span>
                    <flux:badge color="{{ match($version->risk_level) { 'high' => 'red', 'limited' => 'yellow', default => 'green' } }}">
                        {{ ucfirst($version->risk_level) }}
                    </flux:badge>
                </div>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm">
                    <div>
                        <dt class="text-zinc-500">{{ __('agentoom-core::admin.compliance.col_reviewed_by') }}</dt>
                        <dd>{{ $version->reviewed_by ?? '—' }}</dd>
                    </div>
                    @if ($version->use_case_description)
                        <div class="col-span-2">
                            <dt class="text-zinc-500">{{ __('agentoom-core::admin.compliance.version_use_case') }}</dt>
                            <dd>{{ $version->use_case_description }}</dd>
                        </div>
                    @endif
                    @if ($version->mitigations)
                        <div class="col-span-2">
                            <dt class="text-zinc-500">{{ __('agentoom-core::admin.compliance.version_mitigations') }}</dt>
                            <dd>{{ $version->mitigations }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @empty
            <p class="text-zinc-500 text-sm">{{ __('agentoom-core::admin.compliance.version_no_entries') }}</p>
        @endforelse
    </flux:card>
</div>
