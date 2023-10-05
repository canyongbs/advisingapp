<div class="flex flex-col items-center space-x-2 rounded-tl-lg border-b border-gray-200 p-4 dark:border-gray-700">
    <x-filament::input.wrapper
        class="w-full"
        prefix-icon="heroicon-o-magnifying-glass"
    >
        <x-filament::input
            wire:model.live.debounce.150ms="search"
            placeholder="Search Engagements"
        />
    </x-filament::input.wrapper>
</div>
