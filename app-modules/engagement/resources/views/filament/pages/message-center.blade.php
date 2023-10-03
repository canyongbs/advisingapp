<div class="flex max-h-full w-full flex-col p-4 md:flex-row">

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
