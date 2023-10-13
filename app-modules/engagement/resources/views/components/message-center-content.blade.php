<div
    class="col-span-full mt-2 h-full overflow-y-auto rounded-l-lg rounded-r-lg bg-white dark:border-gray-700 dark:bg-gray-900 md:col-span-5 md:mt-0 md:rounded-l-none md:border-l">
    @if ($loadingTimeline)
        <x-filament::loading-indicator class="h-12 w-12" />
    @else
        @if (is_null($educatable))
            <div class="p-4">
                <x-engagement::empty-state />
            </div>
        @else
            <div class="max-h-full overflow-y-auto">
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
