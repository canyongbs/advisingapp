<x-form::blocks.previews.field-wrapper :$label :$required>
    <div class="grid gap-y-2">
        @foreach ($options as $option)
            <div class="flex items-center gap-2">
                <div class="rounded-full w-3 h-3 border border-gray-500"></div>

                <div class="text-sm font-medium leading-6">
                    {{ $option }}
                </div>
            </div>
        @endforeach
    </div>
</x-form::blocks.previews.field-wrapper>
