@php
    use Assist\AssistDataModel\Models\Student;
    use Assist\AssistDataModel\Filament\Resources\StudentResource;
    use Assist\Prospect\Models\Prospect;
    use Assist\Prospect\Filament\Resources\ProspectResource;
@endphp

<x-form::blocks.field-wrapper
    class="py-3"
    :$label
    :$isRequired
>
    @if (filled($response ?? null))
        <div class="not-prose flex flex-wrap items-center gap-3">
            <span>{{ $response ?? null }}</span>
            @if ($authorType === Student::class)
                <a
                    href="{{ StudentResource::getUrl('view', ['record' => $authorKey]) }}"
                    target="_blank"
                >
                    <x-filament::badge color="success">
                        Student
                    </x-filament::badge>
                </a>
            @elseif ($authorType === Prospect::class)
                <a
                    href="{{ ProspectResource::getUrl('view', ['record' => $authorKey]) }}"
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
