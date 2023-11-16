<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    {{ $response ?? null }}

    @if (blank($response ?? null))
        <span class="text-gray-500">No response</span>
    @endif
</x-form::blocks.field-wrapper>
