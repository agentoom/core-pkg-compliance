<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">{{ __('agentoom-core::admin.compliance.incidents') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-core::admin.compliance.incidents_subtitle') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.compliance.incidents.create-edit') }}" icon="plus" variant="primary" size="sm" wire:navigate>
            {{ __('agentoom-core::admin.compliance.new_incident') }}
        </flux:button>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <flux:card>
        <div class="mb-4">
            <flux:select wire:model.live="statusFilter" placeholder="Filter by status…">
                <flux:select.option value="">{{ __('agentoom-core::admin.compliance.all_statuses') }}</flux:select.option>
                <flux:select.option value="open">{{ __('agentoom-core::admin.compliance.status_open') }}</flux:select.option>
                <flux:select.option value="investigating">{{ __('agentoom-core::admin.compliance.status_investigating') }}</flux:select.option>
                <flux:select.option value="resolved">{{ __('agentoom-core::admin.compliance.status_resolved') }}</flux:select.option>
            </flux:select>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_id') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_severity') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_description') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_status') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_reported_by') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_created_at') }}</flux:table.column>
                <flux:table.column>{{ __('agentoom-core::admin.compliance.col_actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($incidents as $incident)
                    <flux:table.row>
                        <flux:table.cell>{{ $incident->id }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ match($incident->severity) { 'critical' => 'red', 'high' => 'orange', 'medium' => 'yellow', default => 'zinc' } }}">
                                {{ ucfirst($incident->severity) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ Str::limit($incident->description, 60) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ match($incident->status) { 'open' => 'red', 'investigating' => 'yellow', 'resolved' => 'green', default => 'zinc' } }}">
                                {{ ucfirst($incident->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $incident->reported_by }}</flux:table.cell>
                        <flux:table.cell>{{ $incident->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="subtle" href="{{ route('admin.compliance.incidents.create-edit', ['incidentId' => $incident->id]) }}" wire:navigate>{{ __('agentoom-core::admin.compliance.edit') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-zinc-500">{{ __('agentoom-core::admin.compliance.incident_no_entries') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $incidents->links() }}
        </div>
    </flux:card>
</div>
