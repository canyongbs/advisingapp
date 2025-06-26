@php
    use Illuminate\Support\HtmlString;

    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<div class="grid divide-gray-950/5 dark:divide-white/10 xl:divide-y">
    <div class="hidden divide-gray-950/5 dark:divide-white/10 xl:flex xl:divide-x xl:divide-y-0">
        <div class="flex-1"></div>

        <div class="grid grid-cols-3 gap-0 divide-x divide-gray-950/5 text-xs dark:divide-white/10">
            @foreach (['Managers', 'Auditors', 'Customers'] as $role)
                <div class="flex w-32 items-center justify-center p-2 text-gray-950 dark:text-white">
                    {{ $role }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="hidden divide-gray-950/5 dark:divide-white/10 xl:flex xl:divide-x xl:divide-y-0">
        <div class="flex-1"></div>

        <div class="grid grid-cols-6 divide-x divide-gray-950/5 text-xs dark:divide-white/10">
            @foreach (['Managers', 'Auditors', 'Customers'] as $role)
                @foreach (['Email', 'App'] as $type)
                    <div class="flex w-16 items-center justify-center p-2 text-center text-gray-950 dark:text-white">
                        {{ $type }}
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="grid divide-y divide-gray-950/5 dark:divide-white/10">
        @foreach ([
        'case_created' => 'Case Created',
        'case_assigned' => 'Case Assigned',
        'case_update' => 'Case Update',
        'case_status_change' => 'Case Status Change',
        'case_closed' => 'Case Closed',
        'survey_response' => 'Survey Response',
    ] as $eventSlug => $event)
            <div
                class="flex flex-col divide-y divide-gray-950/5 dark:divide-white/10 xl:flex-row xl:divide-x xl:divide-y-0">
                <div
                    class="flex items-center px-3 py-2 text-sm text-gray-950 dark:text-white xl:flex-1"
                    @if ($eventSlug === 'case_status_change') x-tooltip.raw="Applies to all status changes other than those in a closed classification" @endif
                >
                    {{ $event }}
                </div>

                <div
                    class="grid grid-cols-1 gap-3 divide-gray-950/5 px-3 py-2 text-sm dark:divide-white/10 xl:grid-cols-3 xl:gap-0 xl:divide-x xl:px-0 xl:py-0">
                    @foreach (['managers' => 'Managers', 'auditors' => 'Auditors', 'customers' => 'Customers'] as $roleSlug => $role)
                        <div class="flex flex-col gap-1 xl:w-32">
                            <div class="xl:hidden">
                                {{ $role }}
                            </div>

                            <div
                                class="grid h-full grid-cols-2 gap-1 divide-gray-950/5 dark:divide-white/10 xl:gap-0 xl:divide-x">
                                @foreach (['email' => 'Email', 'notification' => 'App'] as $typeSlug => $type)
                                    @php
                                        $isSurveyResponse = $eventSlug === 'survey_response';
                                        $shouldShow = !(($isSurveyResponse && in_array($roleSlug, ['managers', 'auditors'])) || ($isSurveyResponse && $roleSlug === 'customers' && $typeSlug === 'notification'));
                                    @endphp

                                    @if ($shouldShow)
                                        <label
                                            class="flex items-center gap-2 xl:flex xl:w-16 xl:justify-center xl:px-3 xl:py-2"
                                        >
                                            <x-filament::input.checkbox
                                                :disabled="$isDisabled"
                                                :wire:model="$statePath . '.is_' . $roleSlug . '_' . $eventSlug . '_' . $typeSlug . '_enabled'"
                                            />
                                            <span class="xl:sr-only">{{ $type }}</span>
                                        </label>
                                    @else
                                        <div class="xl:flex xl:w-16 xl:justify-center xl:px-3 xl:py-2"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
