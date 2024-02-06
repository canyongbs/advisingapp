@php
    use \AdvisingApp\ServiceManagement\Models\ServiceRequest;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <x-timeline::timeline.heading>
            {{
                match($record->related::class) {
                    ServiceRequest::class => 'Auto-response Email sent',
                    default => $record->getKey(),
                }
            }}
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
