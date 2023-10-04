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
