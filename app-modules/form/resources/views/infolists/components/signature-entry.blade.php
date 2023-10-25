<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $getState();
    @endphp

    @if (filled($state))
        <img
            class="h-16 max-w-full"
            src="{{ $state }}"
        />
    @endif
</x-dynamic-component>
