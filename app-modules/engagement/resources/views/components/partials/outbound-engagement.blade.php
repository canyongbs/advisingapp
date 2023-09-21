@php
    use App\Filament\Resources\UserResource;
    use Assist\Engagement\Enums\EngagementDeliveryMethod;
@endphp

<div class="flex-auto rounded-md p-3 ring-1 ring-inset ring-gray-200">
    <div class="flex justify-between gap-x-4">
        <div class="py-0.5 text-xs leading-5 text-gray-500"><span class="font-medium text-gray-900">
                <a
                    class="font-medium text-green-300 underline hover:text-green-400"
                    href="{{ UserResource::getUrl('view', ['record' => $engagement->createdBy]) }}"
                >
                    {{ $engagement->createdBy->name }}
                </a>
        </div>
        <p class="flex-none py-0.5 text-xs leading-5 text-gray-100">
            Sent {{ $engagement->deliver_at->diffForHumans() }}
        </p>
    </div>
    <span class="flex flex-row space-x-2">
        @foreach ($engagement->deliverables as $deliverable)
            @if ($deliverable->channel === EngagementDeliveryMethod::EMAIL)
                <x-filament::icon
                    class="h-5 w-5 text-white"
                    icon="heroicon-o-envelope"
                />
            @endif
            @if ($deliverable->channel === EngagementDeliveryMethod::SMS)
                <x-filament::icon
                    class="h-5 w-5 text-white"
                    icon="heroicon-o-chat-bubble-left"
                />
            @endif
        @endforeach
    </span>
    <div class="mt-4">
        @if (!blank($engagement->subject))
            <h2 class="text-lg">{{ $engagement->subject }}</h2>
        @endif
        <p class="mt-4 text-sm">{{ $engagement->body }}</p>
    </div>

</div>
