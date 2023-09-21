{{-- TODO The timeline record is going to be our base class that holds the --}}
{{-- Default wrapper and styling for the timeline element --}}
{{-- We'll simply inject some data into it, and slot the important data through --}}

@php
    use Assist\Engagement\Models\Engagement;
@endphp

<li class="relative flex gap-x-4">
    @if (!$isLast)
        <div class="absolute -bottom-6 left-0 top-0 flex w-6 justify-center">
            <div class="w-px bg-gray-200"></div>
        </div>
    @endif
    <div class="relative flex h-8 w-8 flex-none items-center justify-center rounded-xl bg-white">
        {{-- TODO Slots --}}
        @if ($engagement instanceof Engagement)
            <x-filament::icon
                class="h-4 w-4 text-gray-900"
                icon="heroicon-o-arrow-small-right"
            />
        @else
            <x-filament::icon
                class="h-4 w-4 text-gray-900"
                icon="heroicon-o-arrow-small-left"
            />
        @endif
    </div>

    @if ($engagement instanceof Engagement)
        <x-engagement::partials.outbound-engagement :engagement="$engagement" />
    @else
        <x-engagement::partials.inbound-engagement :engagement="$engagement" />
    @endif
</li>
