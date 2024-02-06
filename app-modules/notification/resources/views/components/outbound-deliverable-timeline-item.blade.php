@php
    use AdvisingApp\ServiceManagement\Models\ServiceRequest;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <x-timeline::timeline.heading>
            <span class="flex items-center">
                @php
                    $related = $this->record->related;

                    $title = match ($related::class) {
                        ServiceRequest::class => 'Auto-response Email sent',
                        default => $this->record->getKey(),
                    };
                @endphp
                {{ $title }}
            </span>
        </x-timeline::timeline.heading>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <x-timeline::timeline.time>
        {{ $record->created_at->diffForHumans() }}
    </x-timeline::timeline.time>

    <x-timeline::timeline.content>
        {{ $record->content['subject'] }}
    </x-timeline::timeline.content>
</div>
