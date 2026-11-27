{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.
    
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
    same in return. Canyon GBS® and Advising App® are registered trademarks of
    Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
@php
    use AdvisingApp\MeetingCenter\Filament\Widgets\GroupAppointmentCalendarWidget;
    use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages\ListBookingGroups;
@endphp

<x-filament-panels::page>
    <div class="flex w-full justify-start">
        <x-filament::tabs label="Bookings views">
            <x-filament::tabs.item :active="$viewType === 'table'" wire:click="setViewType('table')">
                Bookings
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$viewType === 'calendar'" wire:click="setViewType('calendar')">
                Calendar
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$viewType === 'settings'" wire:click="setViewType('settings')">
                Configuration
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    @if ($viewType === 'table')
        {{ $this->form }}

        <div class="flex flex-col gap-y-6">
            {{ $this->table }}
        </div>
    @elseif ($viewType === 'calendar')
        {{ $this->form }}

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
    @elseif ($viewType === 'settings')
        <div>
            @livewire(ListBookingGroups::class)
        </div>
    @endif
</x-filament-panels::page>
