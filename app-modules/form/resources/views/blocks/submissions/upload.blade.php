<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
@dd($response)
    {{-- @if (filled($response ?? null))
        <a
            href="{{ $response }}"
            target="_blank"
        >
            {{ $response }}
        </a>
    @else
        <span class="text-gray-500">No response</span>
    @endif --}}
</x-form::blocks.field-wrapper>
