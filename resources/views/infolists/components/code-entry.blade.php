<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $getState();
        
        if (is_array($state)) {
            $state = json_encode($state, JSON_PRETTY_PRINT);
        }
    @endphp

    <pre class="whitespace-pre-wrap rounded bg-gray-50 p-4 shadow-sm ring-gray-950/10 dark:bg-gray-950 dark:ring-white/20">{{ $state }}</pre>
</x-dynamic-component>
