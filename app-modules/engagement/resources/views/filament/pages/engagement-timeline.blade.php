<x-filament-panels::page>
    @if ($aggregateEngagements->count() < 1)
        Empty State
    @else
        <ul
            class="space-y-6"
            role="list"
        >
            @foreach ($aggregateEngagements as $engagement)
                <x-engagement::partials.timeline-record
                    :engagement="$engagement"
                    :is-last="$loop->last"
                />
            @endforeach
        </ul>
    @endif

</x-filament-panels::page>
