@php
    use AdvisingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
    use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <x-timeline::timeline.heading>
            Autoresponse Email Sent
        </x-timeline::timeline.heading>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <x-timeline::timeline.time>
        {{ $record->created_at->diffForHumans() }}
    </x-timeline::timeline.time>

    <x-timeline::timeline.content>
        {{ $record->content }}
    </x-timeline::timeline.content>
</div>
