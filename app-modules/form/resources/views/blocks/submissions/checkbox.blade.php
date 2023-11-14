<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    @if (blank($response ?? null))
        <span class="text-gray-500">No response</span>
    @elseif ($response === true)
        <x-heroicon-o-x-circle class="h-6 w-6 text-success-500" />
    @elseif ($response === false)
        <x-heroicon-o-check-circle class="h-6 w-6 text-danger-500" />
    @endif
</x-form::blocks.field-wrapper>
