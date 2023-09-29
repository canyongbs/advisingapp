<div
    class="max-h-screen w-full overflow-y-scroll rounded-tr-lg border-b-2 border-r-2 border-t-2 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
    @if (is_null($educatable))
        <x-engagement::empty-state />
    @else
        <div class="flex flex-col space-y-4">
            @foreach ($educatable->engagements as $engagement)
                <x-engagement::engagement-timeline-item-simple :record="$engagement" />
            @endforeach
        </div>
    @endif
</div>
