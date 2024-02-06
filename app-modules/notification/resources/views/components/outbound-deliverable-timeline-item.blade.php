@php
    use AdvisingApp\ServiceManagement\Models\ServiceRequest;
@endphp

@props(['component', 'record'])
<div>
    <div class="flex flex-row justify-between">
        <x-timeline::timeline.heading>
            <span class="flex items-center">
                @php
                    $related = $record->related;

                    $title = match ($related::class) {
                        ServiceRequest::class => 'Auto-response Email sent',
                        default => $record->getKey(),
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
