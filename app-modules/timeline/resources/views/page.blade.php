<x-filament-panels::page>
    <x-timeline::timeline
        :timelineRecords="$timelineRecords"
        :hasMorePages="$hasMorePages"
        :emptyStateMessage="$emptyStateMessage"
        :noMoreRecordsMessage="$noMoreRecordsMessage"
    />
</x-filament-panels::page>
