{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    same in return. Canyon GBS® and Advising App® are registered trademarks of
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
    use AdvisingApp\MeetingCenter\Filament\Widgets\GroupAppointmentCalendarWidget;
@endphp

<x-filament-panels::page>
    <div class="flex w-full justify-start">
        <div class="grid max-w-xs grid-cols-2 gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800" role="group">
            <button
                type="button"
                @class([
                    'px-5 py-1.5 text-xs font-medium rounded-lg',
                    'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' => $viewType === 'table',
                    'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' => $viewType !== 'table',
                ])
                wire:click="setViewType('table')"
            >
                <x-filament::icon class="h-6 w-6" icon="heroicon-m-table-cells" />
            </button>
            <button
                type="button"
                @class([
                    'px-5 py-1.5 text-xs font-medium rounded-lg',
                    'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' => $viewType === 'calendar',
                    'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' => $viewType !== 'calendar',
                ])
                wire:click="setViewType('calendar')"
            >
                <x-filament::icon class="h-6 w-6" icon="heroicon-m-calendar-days" />
            </button>
        </div>
    </div>

    {{ $this->form }}

    @if ($viewType === 'table')
        <div class="flex flex-col gap-y-6">
            {{ $this->table }}
        </div>
    @elseif ($viewType === 'calendar')
        <div>
            @livewire(
                GroupAppointmentCalendarWidget::class,
                [
                    'groupFilter' => $data['groupFilter'] ?? 'my_groups',
                    'selectedGroupIds' => $data['selectedGroupIds'] ?? [],
                    'hidePast' => $data['hidePast'] ?? true,
                ]
            )
        </div>
    @endif
</x-filament-panels::page>
