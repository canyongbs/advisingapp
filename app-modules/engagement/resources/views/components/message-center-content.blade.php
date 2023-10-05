<div
    class="mt-2 w-full overflow-y-scroll rounded-r-lg border-l bg-white dark:border-gray-700 dark:bg-gray-900 md:mt-0 md:max-h-content">
    @if ($loadingTimeline)
        <x-filament::loading-indicator class="h-12 w-12" />
    @else
        @if (is_null($educatable))
            <div class="p-4">
                <x-engagement::empty-state />
            </div>
        @else
            <div>
                <div
                    class="sticky top-0 z-[5] flex h-12 w-full items-center justify-between bg-gray-100 px-4 dark:bg-gray-700">
                    <h1 class="ml-2">{{ $educatable->display_name }}</h1>
                    <x-filament::button wire:click="engage('{{ $educatable }}')">
                        Engage
                    </x-filament::button>
                </div>
                <div class="p-6">
                    <x-timeline::timeline :aggregateRecords="$aggregateRecords" />
                </div>
            </div>
        @endif
    @endif
</div>
