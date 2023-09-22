@php
    use App\Filament\Resources\UserResource;
    use Assist\Engagement\Enums\EngagementDeliveryMethod;
@endphp

<h3 class="mb-1 flex items-center text-lg font-semibold text-gray-900 dark:text-white">
    <a
        class="font-medium underline"
        href="{{ UserResource::getUrl('view', ['record' => $record->createdBy]) }}"
    >
        {{ $record->createdBy->name }}
    </a>
    <span class="ml-2 flex space-x-2">
        @foreach ($record->deliverables as $deliverable)
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
</h3>
<time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
    Sent {{ $record->deliver_at->diffForHumans() }}
</time>
<p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
    @if (!blank($record->subject))
        <span>{{ $record->subject }}</span>
    @endif
    <span>{{ $record->body }}</span>
</p>
<x-filament::icon-button
    wire:click="viewRecord('{{ $record }}')"
    icon="heroicon-o-eye"
/>
