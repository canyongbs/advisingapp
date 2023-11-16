<x-form::blocks.field-wrapper
    :$label
    :$isRequired
>
    <div class="grid gap-y-2">
        @foreach ($options as $option)
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full border border-gray-500"></div>

                <div class="text-sm font-medium leading-6">
                    {{ $option }}
                </div>
            </div>
        @endforeach
    </div>
</x-form::blocks.field-wrapper>
