{{--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    use Carbon\Carbon;
    use App\Settings\DisplaySettings;
@endphp

<div
    class="col-span-full h-full overflow-y-auto rounded-bl-lg rounded-br-lg rounded-tl-lg rounded-tr-lg bg-white dark:bg-gray-800 sm:rounded-br-none sm:rounded-tr-none md:col-span-3"
    x-data="{ showFilters: false }"
>
    <div class="sticky top-0 z-[5] flex flex-col rounded-tl-lg bg-white dark:bg-gray-800">
        <div class="flex flex-row items-center justify-between p-4">
            <div class="flex flex-col">
                <span class="font-bold text-gray-500 dark:text-gray-400 sm:text-xs md:text-sm">
                    Engagements
                </span>
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    {{ $educatables->total() }} Total Results
                </span>
            </div>
            <x-filament::icon-button
                icon="heroicon-m-funnel"
                x-on:click="showFilters = !showFilters"
                label="Show Filters and Search"
            />
        </div>

        <x-engagement::filters />

        <x-engagement::search />
    </div>

    <div class="hidden flex-col md:flex">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div class="min-w-full">
                        <ul class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach ($educatables as $educatable)
                                <li
                                    @class([
                                        'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700',
                                        'bg-gray-100 dark:bg-gray-700' =>
                                            $selectedEducatable?->identifier() === $educatable->identifier(),
                                    ])
                                    wire:click="selectEducatable('{{ $educatable->identifier() }}', '{{ $educatable->type }}')"
                                >
                                    <div class="justify-left flex flex-col items-center whitespace-nowrap p-4">
                                        <div class="w-full text-base font-normal text-gray-700 dark:text-gray-400">
                                            {{ $educatable->display_name }}
                                        </div>
                                        <div class="w-full text-xs font-normal text-gray-700 dark:text-gray-400">
                                            {{ ucfirst($educatable->type) . ' |' }}
                                            Last engaged at
                                            @php $timezone =  app(DisplaySettings::class)->getTimezone() @endphp
                                            {{ Carbon::parse($educatable->latest_activity)->setTimezone($timezone)->format('g:ia - M j, Y') }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex w-full px-4 py-4 md:hidden">
        <x-filament::input.wrapper class="w-full">
            <x-filament::input.select wire:change="selectChanged($event.target.value)">
                <option value="">Select an engagement</option>
                @foreach ($educatables as $educatable)
                    <option value="{{ $educatable->identifier() }},{{ $educatable->type }}">
                        {{ $educatable->display_name }}
                    </option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>

    {{ $educatables->links() }}
</div>
