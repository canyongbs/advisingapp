@php
    use Carbon\Carbon;
@endphp

<div
    class="border-b-1 max-h-content w-full overflow-y-scroll rounded-l-lg rounded-r-lg border-l-2 border-r-2 border-t-2 border-gray-200 border-r-gray-50 bg-white dark:border-gray-700 dark:bg-gray-800 md:rounded-r-none lg:w-1/3"
    x-data="{ showFilters: false }"
>
    <div
        class="flex items-center justify-between rounded-tl-lg border-b-2 border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col">
            <span class="font-bold text-gray-500 dark:text-gray-400 sm:text-xs md:text-sm">
                Engagements
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500">
                Showing
                {{ $educatables->total() < $educatables->perPage() ? $educatables->total() : $educatables->perPage() }}
                of {{ $educatables->total() }}
            </span>
        </div>
        <x-filament::icon-button
            icon="heroicon-m-funnel"
            x-on:click="showFilters = !showFilters"
            label="Show Filters and Search"
        />
    </div>
    <div
        class="flex flex-col items-start justify-start space-y-2 rounded-tl-lg border-b-2 border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
        x-show="showFilters"
    >
        {{-- TODO Potentially extract these filters --}}
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="peopleScope">
                <option value="all">All</option>
                <option value="students">Students</option>
                <option value="prospects">Prospects</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
        <div class="flex w-full flex-col space-y-2">
            <span>Date Range</span>

            <div
                class="flex w-full flex-col items-center"
                date-rangepicker
            >
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
        <label>
            <x-filament::input.checkbox wire:model.live="filterSubscribed" />

            <span>
                My Subscriptions
            </span>
        </label>
        <label>
            <x-filament::input.checkbox wire:model.live="filterOpenTasks" />
            <span>
                Open Tasks
            </span>
        </label>

        <label>
            <x-filament::input.checkbox wire:model.live="filterOpenServiceRequests" />
            <span>
                Open Service Requests
            </span>
        </label>

    </div>
    <div
        class="flex flex-col items-center space-x-2 rounded-tl-lg border-b-2 border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <x-filament::input.wrapper class="mt-2 w-full">
            <x-filament::input
                type="text"
                wire:model.live.debounce.150ms="search"
                placeholder="Search Engagements..."
            />
        </x-filament::input.wrapper>
    </div>
    <div class="hidden flex-col md:flex">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div class="min-w-full divide-y divide-gray-200">
                        <ul class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach ($educatables as $educatable)
                                <li
                                    @class([
                                        'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700',
                                        'bg-gray-100 dark:bg-gray-700' =>
                                            $selectedEducatable?->identifier() === $educatable->identifier(),
                                    ])
                                    wire:click="selectEducatable('{{ $educatable->identifier() }}', '{{ $educatable->getMorphClass() }}')"
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
    <div class="flex w-full md:hidden">
        <x-filament::input.wrapper class="w-full">
            <x-filament::input.select wire:change="selectChanged($event.target.value)">
                <option value="">Select an engagement</option>
                @foreach ($educatables as $educatable)
                    <option value="{{ $educatable->identifier() }},{{ $educatable->getMorphClass() }}">
                        {{ $educatable->display_name }}
                    </option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>
</div>
