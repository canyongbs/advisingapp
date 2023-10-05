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

        <div class="flex w-full flex-col items-center">
            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-filament::icon
                        class="h-4 w-4 text-gray-500 dark:text-gray-400"
                        icon="heroicon-o-calendar-days"
                    />
                </div>
                <input
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 pl-10 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    name="filterStartDate"
                    type="date"
                    wire:model.live="filterStartDate"
                    placeholder="Start"
                >
            </div>
            <span class="mx-4 text-gray-500">to</span>
            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-filament::icon
                        class="h-4 w-4 text-gray-500 dark:text-gray-400"
                        icon="heroicon-o-calendar-days"
                    />
                </div>
                <input
                    class="block w-full rounded-lg border border-gray-300 bg-transparent p-2.5 pl-10 text-sm text-gray-900 dark:border-gray-600 dark:text-white"
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

<style>
    input[type="date"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: none;
        -webkit-appearance: none;
    }
</style>
