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
<div
    class="flex flex-col items-start justify-start space-y-4 bg-white p-4 text-sm font-normal text-gray-700 dark:bg-gray-800 dark:text-gray-400"
    x-show="showFilters"
>
    <span>Filter Engagements by:</span>
    <x-filament::input.wrapper>

        <x-filament::input.select wire:model.live="filterPeopleType">
            <option value="all">All</option>
            <option value="students">Students</option>
            <option value="prospects">Prospects</option>
        </x-filament::input.select>
    </x-filament::input.wrapper>

    <div class="flex w-full flex-col space-y-2">
        <span>Date Range</span>

        <div class="flex w-full flex-col items-center space-y-2">
            <div class="w-full">
                <input
                    class="!focus:ring-trout-700 text-assist-black-950 dark:text-assist-white-50 block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-bright-sun-300"
                    name="filterStartDate"
                    type="date"
                    wire:model.live="filterStartDate"
                    placeholder="Start"
                >
            </div>
            <span class="mx-4 text-gray-500">to</span>
            <div class="w-full">
                <input
                    class="!focus:ring-trout-700 text-assist-black-950 dark:text-assist-white-50 block w-full rounded-lg border border-gray-300 bg-transparent p-2.5 text-sm dark:border-gray-600 dark:focus:ring-bright-sun-300"
                    name="filterEndDate"
                    type="date"
                    wire:model.live="filterEndDate"
                    placeholder="End"
                >
            </div>
        </div>
    </div>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterSubscribed" />

        <span class="ml-2">
            My Subscriptions
        </span>
    </label>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterOpenTasks" />
        <span class="ml-2">
            Open Tasks
        </span>
    </label>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterOpenServiceRequests" />
        <span class="ml-2">
            Open Service Requests
        </span>
    </label>
</div>
