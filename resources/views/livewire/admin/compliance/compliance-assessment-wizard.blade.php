<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('admin.compliance.governed-deployments.index') }}" variant="ghost" icon="arrow-left" size="sm">{{ __('agentoom-compliance::admin.compliance.back') }}</flux:button>
        <flux:heading size="xl">{{ __('agentoom-compliance::admin.compliance.wizard_title') }}</flux:heading>
    </div>

    <div class="max-w-2xl">
        {{-- Step indicator --}}
        <div class="flex items-center gap-2 mb-8">
            @foreach([1 => __('agentoom-compliance::admin.compliance.wizard_step_basics'), 2 => __('agentoom-compliance::admin.compliance.wizard_step_details'), 3 => __('agentoom-compliance::admin.compliance.wizard_step_result'), 4 => __('agentoom-compliance::admin.compliance.wizard_step_done')] as $s => $label)
                <div class="flex items-center gap-1">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $step >= $s ? 'bg-blue-600 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500' }}">{{ $s }}</div>
                    <span class="text-xs {{ $step >= $s ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400' }}">{{ $label }}</span>
                </div>
                @if($s < 4)<div class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></div>@endif
            @endforeach
        </div>

        {{-- Step 1: Basics --}}
        @if($step === 1)
            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('agentoom-compliance::admin.compliance.wizard_name') }}</flux:label>
                    <flux:input wire:model="name" placeholder="{{ __('agentoom-compliance::admin.compliance.wizard_name_placeholder') }}" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('agentoom-compliance::admin.compliance.wizard_agent') }}</flux:label>
                    <flux:text class="text-xs text-zinc-500 mb-1">{{ __('agentoom-compliance::admin.compliance.wizard_agent_hint') }}</flux:text>
                    <flux:select wire:model="agentId">
                        <flux:select.option value="">{{ __('agentoom-compliance::admin.compliance.wizard_agent_none') }}</flux:select.option>
                        @foreach(\Agentoom\Core\Models\Agent::orderBy('name')->get() as $agent)
                            <flux:select.option value="{{ $agent->id }}">{{ $agent->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('agentoom-compliance::admin.compliance.wizard_ai_purpose') }}</flux:label>
                    <flux:select wire:model="aiPurpose">
                        <flux:select.option value="">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_select') }}</flux:select.option>
                        <flux:select.option value="sales_analysis">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_sales') }}</flux:select.option>
                        <flux:select.option value="inventory">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_inventory') }}</flux:select.option>
                        <flux:select.option value="accounting">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_accounting') }}</flux:select.option>
                        <flux:select.option value="customer_support">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_customer_support') }}</flux:select.option>
                        <flux:select.option value="workflow_automation">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_workflow') }}</flux:select.option>
                        <flux:select.option value="marketing">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_marketing') }}</flux:select.option>
                        <flux:select.option value="candidate_screening">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_candidate_screening') }}</flux:select.option>
                        <flux:select.option value="employee_evaluation">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_employee_evaluation') }}</flux:select.option>
                        <flux:select.option value="healthcare_support">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_healthcare') }}</flux:select.option>
                        <flux:select.option value="education_support">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_education') }}</flux:select.option>
                        <flux:select.option value="finance_decisions">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_finance') }}</flux:select.option>
                        <flux:select.option value="other">{{ __('agentoom-compliance::admin.compliance.wizard_purpose_other') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="aiPurpose" />
                </flux:field>

                <flux:button wire:click="nextStep" variant="primary">{{ __('agentoom-compliance::admin.compliance.wizard_next') }}</flux:button>
            </div>
        @endif

        {{-- Step 2: Details --}}
        @if($step === 2)
            <div class="space-y-6">
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">{{ __('agentoom-compliance::admin.compliance.wizard_ai_actions') }}</p>
                    @foreach(['recommend_actions' => __('agentoom-compliance::admin.compliance.wizard_action_recommend'), 'score_people' => __('agentoom-compliance::admin.compliance.wizard_action_score'), 'rank_people' => __('agentoom-compliance::admin.compliance.wizard_action_rank'), 'approve_reject_people' => __('agentoom-compliance::admin.compliance.wizard_action_approve_reject')] as $value => $label)
                        <label class="flex items-center gap-2 mb-2 cursor-pointer">
                            <input type="checkbox" wire:model="aiActions" value="{{ $value }}" class="rounded" />
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">{{ __('agentoom-compliance::admin.compliance.wizard_affected') }}</p>
                    @foreach(['employees' => __('agentoom-compliance::admin.compliance.wizard_affected_employees'), 'customers' => __('agentoom-compliance::admin.compliance.wizard_affected_customers'), 'applicants' => __('agentoom-compliance::admin.compliance.wizard_affected_applicants'), 'students' => __('agentoom-compliance::admin.compliance.wizard_affected_students'), 'patients' => __('agentoom-compliance::admin.compliance.wizard_affected_patients'), 'citizens' => __('agentoom-compliance::admin.compliance.wizard_affected_citizens')] as $value => $label)
                        <label class="flex items-center gap-2 mb-2 cursor-pointer">
                            <input type="checkbox" wire:model="affectedGroups" value="{{ $value }}" class="rounded" />
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <flux:field>
                    <flux:label>{{ __('agentoom-compliance::admin.compliance.wizard_human_review') }}</flux:label>
                    <flux:select wire:model="humanReviewFrequency">
                        <flux:select.option value="always">{{ __('agentoom-compliance::admin.compliance.wizard_review_always') }}</flux:select.option>
                        <flux:select.option value="usually">{{ __('agentoom-compliance::admin.compliance.wizard_review_usually') }}</flux:select.option>
                        <flux:select.option value="sometimes">{{ __('agentoom-compliance::admin.compliance.wizard_review_sometimes') }}</flux:select.option>
                        <flux:select.option value="never">{{ __('agentoom-compliance::admin.compliance.wizard_review_never') }}</flux:select.option>
                    </flux:select>
                </flux:field>

                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">{{ __('agentoom-compliance::admin.compliance.wizard_data_processed') }}</p>
                    @foreach(['business_data' => __('agentoom-compliance::admin.compliance.wizard_data_business'), 'customer_data' => __('agentoom-compliance::admin.compliance.wizard_data_customer'), 'employee_data' => __('agentoom-compliance::admin.compliance.wizard_data_employee'), 'financial_data' => __('agentoom-compliance::admin.compliance.wizard_data_financial'), 'health_data' => __('agentoom-compliance::admin.compliance.wizard_data_health'), 'children_data' => __('agentoom-compliance::admin.compliance.wizard_data_children')] as $value => $label)
                        <label class="flex items-center gap-2 mb-2 cursor-pointer">
                            <input type="checkbox" wire:model="dataProcessed" value="{{ $value }}" class="rounded" />
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="flex gap-3">
                    <flux:button wire:click="prevStep" variant="ghost">{{ __('agentoom-compliance::admin.compliance.wizard_back') }}</flux:button>
                    <flux:button wire:click="classify" variant="primary">{{ __('agentoom-compliance::admin.compliance.wizard_classify_btn') }}</flux:button>
                </div>
            </div>
        @endif

        {{-- Step 3: Result --}}
        @if($step === 3 && $classificationResult)
            <div class="space-y-4">
                <div class="rounded-lg border p-5 space-y-3
                    {{ $classificationResult['governance_level'] === 'enhanced' ? 'border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800' :
                       ($classificationResult['governance_level'] === 'standard' ? 'border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-800' :
                       'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800') }}">

                    <p class="text-sm font-semibold">
                        {{ __('agentoom-compliance::admin.compliance.wizard_result_level') }}
                        <span class="text-base">{{ ucfirst($classificationResult['governance_level']) }}</span>
                    </p>

                    @if($classificationResult['potentially_sensitive'])
                        <p class="text-sm font-medium text-orange-700 dark:text-orange-400">{{ __('agentoom-compliance::admin.compliance.wizard_sensitive_flag') }}</p>
                    @endif

                    <p class="text-sm">{{ $classificationResult['reasoning'] }}</p>

                    <div>
                        <p class="text-xs font-medium mb-1">{{ __('agentoom-compliance::admin.compliance.wizard_controls_label') }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($classificationResult['recommended_controls'] as $control)
                                <flux:badge size="sm" color="zinc">{{ str_replace('_', ' ', $control) }}</flux:badge>
                            @endforeach
                        </div>
                    </div>

                    <p class="text-xs text-zinc-500 dark:text-zinc-400 italic">
                        {{ __('agentoom-compliance::admin.compliance.wizard_disclaimer') }}
                    </p>
                </div>

                <div class="flex gap-3">
                    <flux:button wire:click="prevStep" variant="ghost">{{ __('agentoom-compliance::admin.compliance.wizard_back') }}</flux:button>
                    <flux:button wire:click="save" variant="primary">{{ __('agentoom-compliance::admin.compliance.wizard_save') }}</flux:button>
                </div>
            </div>
        @endif

        {{-- Step 4: Done --}}
        @if($step === 4)
            <div class="text-center space-y-4 py-8">
                <div class="text-4xl">✓</div>
                <flux:heading size="lg">{{ __('agentoom-compliance::admin.compliance.wizard_done_heading') }}</flux:heading>
                <p class="text-zinc-500">{{ __('agentoom-compliance::admin.compliance.wizard_done_body') }}</p>
                <div class="flex justify-center gap-3">
                    <flux:button href="{{ route('admin.compliance.governed-deployments.index') }}" variant="primary">{{ __('agentoom-compliance::admin.compliance.wizard_view_deployments') }}</flux:button>
                    <flux:button wire:click="$set('step', 1)" variant="ghost">{{ __('agentoom-compliance::admin.compliance.wizard_new_assessment') }}</flux:button>
                </div>
            </div>
        @endif
    </div>
</div>
