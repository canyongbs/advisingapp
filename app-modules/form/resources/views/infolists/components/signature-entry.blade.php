<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $getState();
    @endphp

    @if (filled($state))
        <div class="dark:bg-white">
            <img
                class="h-16 max-w-full"
                src="{{ $state }}"
            />
        </div>
    @endif
</x-dynamic-component>
