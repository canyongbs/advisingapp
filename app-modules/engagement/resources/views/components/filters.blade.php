<div
    class="flex flex-col items-start justify-start space-y-2 rounded-tl-lg border-b-2 border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
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

        <div class="flex w-full flex-col items-center">
            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-engagement::svg.calendar />
                </div>
                <input
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    name="filterStartDate"
                    type="date"
                    wire:model.live="filterStartDate"
                    placeholder="Start"
                >
            </div>
            <span class="mx-4 text-gray-500">to</span>
            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-engagement::svg.calendar />
                </div>
                <input
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
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
