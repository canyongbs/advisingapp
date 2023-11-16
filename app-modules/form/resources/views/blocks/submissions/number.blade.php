<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    @if (filled($response ?? null))
        {{ \Filament\Support\format_number($response ?? null) }}
    @else
        <span class="text-gray-500">No response</span>
    @endif
</x-form::blocks.field-wrapper>
