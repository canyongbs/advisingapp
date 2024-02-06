@php
    use \AdvisingApp\ServiceManagement\Models\ServiceRequest;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <x-timeline::timeline.heading>
            <span class="flex items-center">
                {{
                    match($record->related::class) {
                        ServiceRequest::class => 'Auto-response Email sent',
                        default => $record->getKey(),
                    }
                }}
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
        {{--        Subject: {{ $record->content['subject'] }}--}}
        {{--        <br><br>--}}
        {{--        {{ $record->content['greeting'] }}--}}
        {{--        @foreach($record->content['introLines'] as $line)--}}
        {{--            <br><br>--}}
        {{--            {{ $line }}--}}
        {{--        @endforeach--}}
        {{--        <br><br>--}}
        {{--        @if (! empty($record->content['salutation']))--}}
        {{--            {{ $record->content['salutation'] }}--}}
        {{--        @else--}}
        {{--            @lang('Regards'),<br>--}}
        {{--            {{ config('app.name') }}--}}
        {{--        @endif--}}
        {{ $record->content['subject'] }}
    </x-timeline::timeline.content>
</div>
