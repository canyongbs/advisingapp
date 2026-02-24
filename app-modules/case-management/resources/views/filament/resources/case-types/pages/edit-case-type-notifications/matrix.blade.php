{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.
    
    Notice:
    
    - You may not provide the software to third parties as a hosted or managed
    service, where the service provides users with access to any substantial set of
    the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
    in the software, and you may not remove or obscure any functionality in the
    software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
    of the licensor in the software. Any use of the licensor’s trademarks is subject
    to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
    same in return. Canyon GBS™ and Advising App™ are registered trademarks of
    Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
@php
    use Illuminate\Support\HtmlString;
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<div class="divide-gray-950/5 grid xl:divide-y dark:divide-white/10">
    <div class="divide-gray-950/5 hidden xl:flex xl:divide-x xl:divide-y-0 dark:divide-white/10">
        <div class="flex-1"></div>

        <div class="divide-gray-950/5 grid grid-cols-3 gap-0 divide-x text-xs dark:divide-white/10">
            @foreach (['Managers', 'Auditors', 'Customers'] as $role)
                <div class="flex w-32 items-center justify-center p-2 text-gray-950 dark:text-white">
                    {{ $role }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="divide-gray-950/5 hidden xl:flex xl:divide-x xl:divide-y-0 dark:divide-white/10">
        <div class="flex-1"></div>

        <div class="divide-gray-950/5 grid grid-cols-6 divide-x text-xs dark:divide-white/10">
            @foreach (['Managers', 'Auditors', 'Customers'] as $role)
                @foreach (['Email', 'App'] as $type)
                    <div class="flex w-16 items-center justify-center p-2 text-center text-gray-950 dark:text-white">
                        {{ $type }}
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="divide-gray-950/5 grid divide-y dark:divide-white/10">
        @foreach ([
                'case_created' => 'Case Created',
                'case_assigned' => 'Case Assigned',
                'case_update' => 'Case Update',
                'case_status_change' => 'Case Status Change',
                'case_closed' => 'Case Closed',
                'survey_response' => 'Survey Response'
            ]
            as $eventSlug => $event)
            <div
                class="divide-gray-950/5 flex flex-col divide-y xl:flex-row xl:divide-x xl:divide-y-0 dark:divide-white/10"
            >
                <div
                    class="flex items-center px-3 py-2 text-sm text-gray-950 xl:flex-1 dark:text-white"
                    @if ($eventSlug === 'case_status_change') x-tooltip.raw="Applies to all status changes other than those in a closed classification" @endif
                >
                    {{ $event }}
                </div>

                <div
                    class="divide-gray-950/5 grid grid-cols-1 gap-3 px-3 py-2 text-sm xl:grid-cols-3 xl:gap-0 xl:divide-x xl:px-0 xl:py-0 dark:divide-white/10"
                >
                    @foreach (['managers' => 'Managers', 'auditors' => 'Auditors', 'customers' => 'Customers'] as $roleSlug => $role)
                        <div class="flex flex-col gap-1 xl:w-32">
                            <div class="xl:hidden">
                                {{ $role }}
                            </div>

                            <div
                                class="divide-gray-950/5 grid h-full grid-cols-2 gap-1 xl:gap-0 xl:divide-x dark:divide-white/10"
                            >
                                @foreach (['email' => 'Email', 'notification' => 'App'] as $typeSlug => $type)
                                    @php
                                        $isSurveyResponse = $eventSlug === 'survey_response';
                                        $shouldShow = ! (
                                            ($isSurveyResponse && in_array($roleSlug, ['managers', 'auditors'])) ||
                                            ($isSurveyResponse && $roleSlug === 'customers' && $typeSlug === 'notification')
                                        );
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
