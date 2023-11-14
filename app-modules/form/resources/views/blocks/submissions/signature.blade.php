<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    <div class="flex h-16 max-w-xs items-center dark:bg-white">
        @if (filled($response ?? null))
            <img
                class="h-16 max-w-xs"
                src="{{ $response }}"
            />
        @else
            <div class="px-3 text-gray-500">
                No response
            </div>
        @endif
    </div>
</x-form::blocks.field-wrapper>
