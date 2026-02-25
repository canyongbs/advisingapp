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
    use AdvisingApp\Authorization\Enums\LicenseType;
    use AdvisingApp\Prospect\Models\Prospect;
    use AdvisingApp\StudentDataModel\Models\Student;
@endphp

<div
    class="flex flex-col items-start justify-start space-y-4 bg-white p-4 text-sm font-normal text-gray-700 dark:bg-gray-800 dark:text-gray-400"
    x-show="showFilters"
>
    @if (auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()]))
        <span>Filter Engagements by:</span>

        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="filterPeopleType">
                <option value="all">All</option>
                <option value="students">Students</option>
                <option value="prospects">Prospects</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
    @endif

    <div class="flex w-full flex-col space-y-2">
        <span>Date Range</span>

        <div class="flex w-full flex-col items-center space-y-2">
            <div class="w-full">
                <input
                    class="!focus:ring-trout-700 text-advising-app-black-950 dark:text-advising-app-white-50 block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-bright-sun-300"
                    name="filterStartDate"
                    type="date"
                    wire:model.live="filterStartDate"
                    placeholder="Start"
                />
            </div>
            <span class="mx-4 text-gray-500">to</span>
            <div class="w-full">
                <input
                    class="!focus:ring-trout-700 text-advising-app-black-950 dark:text-advising-app-white-50 block w-full rounded-lg border border-gray-300 bg-transparent p-2.5 text-sm dark:border-gray-600 dark:focus:ring-bright-sun-300"
                    name="filterEndDate"
                    type="date"
                    wire:model.live="filterEndDate"
                    placeholder="End"
                />
            </div>
        </div>
    </div>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterMemberOfCareTeam" />
        <span class="ml-2">Member of Care team</span>
    </label>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterSubscribed" />

        <span class="ml-2">My Subscriptions</span>
    </label>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterInboundMessages" />

        <span class="ml-2">Inbound Messages</span>
    </label>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterOpenTasks" />
        <span class="ml-2">Open Tasks</span>
    </label>

    <label class="flex items-center">
        <x-filament::input.checkbox wire:model.live="filterOpenCases" />
        <span class="ml-2">Open Cases</span>
    </label>
</div>
