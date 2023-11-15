<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    @if (filled($response ?? null))
        <div class="not-prose flex flex-wrap items-center gap-3">
            <span>{{ $response ?? null }}</span>
            @if ($authorType === \Assist\AssistDataModel\Models\Student::class)
                <a
                    href="{{ \Assist\AssistDataModel\Filament\Resources\StudentResource::getUrl('view', ['record' => $authorKey]) }}"
                    target="_blank"
                >
                    <x-filament::badge color="success">
                        Student
                    </x-filament::badge>
                </a>
            @elseif ($authorType === \Assist\Prospect\Models\Prospect::class)
                <a
                    href="{{ \Assist\Prospect\Filament\Resources\ProspectResource::getUrl('view', ['record' => $authorKey]) }}"
                    target="_blank"
                >
                    <x-filament::badge color="success">
                        Prospect
                    </x-filament::badge>
                </a>
            @else
                <x-filament::badge color="danger">
                    Not found
                </x-filament::badge>
            @endif
        </div>
    @else
        <span class="text-gray-500">No response</span>
    @endif
</x-form::blocks.field-wrapper>
