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
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Facades\FilamentAsset;
    use AdvisingApp\MeetingCenter\Filament\Widgets\CalendarEventWidget;
    use AdvisingApp\MeetingCenter\Filament\Pages\ManagePersonalBookingPage;
    use App\Filament\Pages\ConnectedAccounts;
@endphp

<x-filament-panels::page
    @class(['fi-resource-list-records-page', 'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug())])
>
    {{-- TODO: Determine the best way to check if calendar is set up --}}
    @empty(auth()->user()->calendar?->oauth_token)
        <div wire:init="mountAction('setupCalendarProviderAction')"></div>
    @endempty

    @if (auth()->user()->calendar?->oauth_token && ! auth()->user()->calendar?->provider_id)
        <div wire:init="mountAction('selectCalendarAction')"></div>
    @endif

    <div class="flex w-full justify-start">
        <x-filament::tabs label="My Calendar views">
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
        <div class="flex flex-col gap-y-6">
            @if (count($tabs = $this->getTabs()))
                <x-filament::tabs>
                    {{ FilamentView::renderHook('panels::resource.pages.list-records.tabs.start', scopes: $this->getRenderHookScopes()) }}

                    @foreach ($tabs as $tabKey => $tab)
                        @php
                            $activeTab = strval($activeTab);
                            $tabKey = strval($tabKey);
                        @endphp

                        <x-filament::tabs.item
                            :active="$activeTab === $tabKey"
                            :badge="$tab->getBadge()"
                            :icon="$tab->getIcon()"
                            :icon-position="$tab->getIconPosition()"
                            :wire:click="'$set(\'activeTab\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                        >
                            {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
                        </x-filament::tabs.item>
                    @endforeach

                    {{ FilamentView::renderHook('panels::resource.pages.list-records.tabs.end', scopes: $this->getRenderHookScopes()) }}
                </x-filament::tabs>
            @endif

            {{ FilamentView::renderHook('panels::resource.pages.list-records.table.before', scopes: $this->getRenderHookScopes()) }}

            {{ $this->table }}

            {{ FilamentView::renderHook('panels::resource.pages.list-records.table.after', scopes: $this->getRenderHookScopes()) }}
        </div>
    @elseif ($viewType === 'calendar')
        <div>
            @livewire(CalendarEventWidget::class)
        </div>
    @elseif ($viewType === 'settings')
        <div class="grid gap-y-6">
            @livewire(ManagePersonalBookingPage::class)
            @livewire(ConnectedAccounts::class)
        </div>
    @endif
</x-filament-panels::page>
