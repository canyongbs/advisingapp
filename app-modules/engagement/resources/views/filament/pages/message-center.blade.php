<x-filament-panels::page
    class="flex h-full flex-col"
    style="max-height: calc(100vh - 8rem)"
    full-height="true"
>
    <div
        class="h-full w-full flex-1 flex-col rounded-lg border-0 border-gray-200 dark:border-gray-700 md:overflow-y-auto md:border">
        <div class="grid h-full grid-cols-1 md:grid-cols-8">
            @if ($loadingInbox)
                <x-filament::loading-indicator class="col-span-full h-12 w-12" />
            @else
                <x-engagement::message-center-inbox
                    :selectedEducatable="$selectedEducatable"
                    :educatables="$educatables"
                />

                <x-engagement::message-center-content
                    :loadingTimeline="$loadingTimeline"
                    :educatable="$selectedEducatable"
                    :aggregateRecords="$aggregateRecordsForEducatable"
                />
            @endif
        </div>
    </div>
</x-filament-panels::page>
