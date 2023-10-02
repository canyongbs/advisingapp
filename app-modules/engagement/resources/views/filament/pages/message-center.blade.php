<div class="flex max-h-full w-full flex-row p-4">

    @if ($loadingInbox)
        <x-filament::loading-indicator class="h-12 w-12" />
    @else
        <x-engagement::message-center-inbox
            :selectedEducatable="$selectedEducatable"
            :educatables="$educatables"
        />

        <x-engagement::message-center-content
            :loadingTimeline="$loadingTimeline"
            :educatable="$selectedEducatable"
            :aggregateRecords="$aggregateRecords"
        />
    @endif

</div>
