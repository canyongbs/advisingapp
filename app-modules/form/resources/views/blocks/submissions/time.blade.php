<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    @if (filled($response ?? null))
        {{ \Carbon\Carbon::parse($response)->toTimeString(unitPrecision: 'minute') }}
    @else
        <span class="text-gray-500">No response</span>
    @endif
</x-form::blocks.field-wrapper>
