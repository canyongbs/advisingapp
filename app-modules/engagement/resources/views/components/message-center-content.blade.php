<div
    class="max-h-content w-full overflow-y-scroll rounded-r-lg border-b-2 border-r-2 border-t-2 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
    @if ($loadingTimeline)
        <x-filament::loading-indicator class="h-12 w-12" />
    @else
        @if (is_null($educatable))
            <x-engagement::empty-state />
        @else
            <x-timeline::timeline :aggregateRecords="$aggregateRecords" />
        @endif
    @endif
</div>
