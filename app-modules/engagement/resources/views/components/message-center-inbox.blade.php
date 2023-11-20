{{--
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}
@php
    use Carbon\Carbon;
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
                                            Last engaged at

                                            {{ Carbon::parse($educatable->latest_activity)->format('g:ia - M j, Y') }}
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
