<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.agent_traces') }}</flux:heading>
            <flux:subheading>{{ __('agentoom-compliance::admin.compliance.agent_traces_subtitle') }}</flux:subheading>
        </div>
    </div>

    <flux:card class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search traces..." />

            <flux:select wire:model.live="agentId">
                <flux:select.option value="">All Agents</flux:select.option>
                @foreach($agents as $agent)
                    <flux:select.option value="{{ $agent->id }}">{{ $agent->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live.debounce.300ms="conversationId" placeholder="Conversation ID" />

            <flux:button variant="subtle" wire:click="clearFilters">Clear Filters</flux:button>
        </div>
    </flux:card>

    <flux:card>
        @if ($selectedConversationId)
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <flux:button icon="chevron-left" variant="subtle" wire:click="clearConversation">Back to Conversations</flux:button>
                    <flux:heading size="lg">Conversation: {{ $selectedConversationId }}</flux:heading>
                </div>

                <flux:table :paginate="$traces">
                    <flux:table.columns>
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Agent</flux:table.column>
                        <flux:table.column>Conversation</flux:table.column>
                        <flux:table.column>Model</flux:table.column>
                        <flux:table.column>Steps</flux:table.column>
                        <flux:table.column>Tokens</flux:table.column>
                        <flux:table.column>Time</flux:table.column>
                        <flux:table.column align="right">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($traces as $trace)
                            <flux:table.row wire:key="trace-{{ $trace->id }}">
                                <flux:table.cell>
                                    <span class="text-zinc-500 whitespace-nowrap">{{ $trace->created_at->format('Y-m-d H:i:s') }}</span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <span class="font-medium">{{ $trace->agent?->name ?? 'Unknown' }}</span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <span class="text-zinc-500">{{ Str::limit($trace->conversation_id, 8) }}</span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" color="zinc" inset="top bottom">{{ $trace->model }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" color="sky" inset="top bottom">{{ $trace->total_steps }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <span class="text-zinc-500 tabular-nums">{{ number_format($trace->total_tokens) }}</span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <span class="text-zinc-500 tabular-nums">{{ $trace->execution_time_ms ? $trace->execution_time_ms . 'ms' : '-' }}</span>
                                </flux:table.cell>

                                <flux:table.cell align="right">
                                    <flux:modal.trigger name="trace-details-{{ $trace->id }}">
                                        <flux:button size="sm" variant="ghost" icon="eye"></flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal name="trace-details-{{ $trace->id }}" class="md:w-[800px]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Trace Details #{{ $trace->id }}</flux:heading>
                                                <flux:subheading>Steps taken by the agent to generate the response.</flux:subheading>
                                            </div>

                                            <div class="space-y-4">
                                                @foreach($trace->steps_json ?? [] as $step)
                                                    <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Step {{ $step['step_number'] }}</span>
                                                        </div>

                                                        @if(!empty($step['thinking']))
                                                            <div class="mb-4 p-3 rounded bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-400 text-xs italic text-zinc-600 dark:text-zinc-400">
                                                                <div class="font-bold mb-1 not-italic">Thinking...</div>
                                                                {!! nl2br(e($step['thinking'])) !!}
                                                            </div>
                                                        @endif

                                                        @if(!empty($step['text']))
                                                            <div class="prose prose-sm dark:prose-invert max-w-none">
                                                                {!! nl2br(e($step['text'])) !!}
                                                            </div>
                                                        @endif

                                                        @if(!empty($step['tool_calls']))
                                                            <div class="space-y-2 mt-4">
                                                                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Tool Calls</span>
                                                                @foreach($step['tool_calls'] as $toolCall)
                                                                    <div class="p-2 rounded bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-xs">
                                                                        <div class="font-mono text-sky-600 dark:text-sky-400">{{ $toolCall['name'] }}()</div>
                                                                        <pre class="mt-1 text-[10px] text-zinc-500 overflow-x-auto">{{ json_encode($toolCall['arguments'], JSON_PRETTY_PRINT) }}</pre>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        @if(!empty($step['tool_results']))
                                                            <div class="space-y-2 mt-4">
                                                                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Tool Results</span>
                                                                @foreach($step['tool_results'] as $toolResult)
                                                                    <div class="p-2 rounded bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 text-xs">
                                                                        <div class="font-mono text-emerald-600 dark:text-emerald-400">{{ $toolResult['name'] }} result:</div>
                                                                        <pre class="mt-1 text-[10px] text-zinc-500 overflow-x-auto">{{ is_string($toolResult['result']) ? $toolResult['result'] : json_encode($toolResult['result'], JSON_PRETTY_PRINT) }}</pre>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="flex justify-end">
                                                <flux:button variant="ghost" x-on:click="$el.closest('dialog').close()">Close</flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500">
                                    No traces found.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        @else
            <flux:table :paginate="$conversations">
                <flux:table.columns>
                    <flux:table.column>Last Message</flux:table.column>
                    <flux:table.column>Agent</flux:table.column>
                    <flux:table.column>Conversation</flux:table.column>
                    <flux:table.column>Model</flux:table.column>
                    <flux:table.column>Traces</flux:table.column>
                    <flux:table.column align="right">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($conversations as $trace)
                        <flux:table.row wire:key="conv-{{ $trace->conversation_id }}">
                            <flux:table.cell>
                                <span class="text-zinc-500 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($trace->conversation?->messages_max_created_at ?? $trace->created_at)->format('Y-m-d H:i:s') }}</span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-medium">{{ $trace->agent?->name ?? 'Unknown' }}</span>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ Str::limit($trace->conversation_id, 8) }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $trace->model }}</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="sky" inset="top bottom">{{ $trace->conversation?->traces_count ?? 0 }}</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                <flux:button size="sm" variant="ghost" icon="eye" wire:click="selectConversation('{{ $trace->conversation_id }}')"></flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                No conversations found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>
</div>
