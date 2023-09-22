@php
    use App\Filament\Resources\UserResource;
    use Assist\Engagement\Enums\EngagementDeliveryMethod;
@endphp

<div>
    <div class="flex flex-row justify-between">
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
        <x-filament::icon-button
            wire:click="viewRecord('{{ $record }}')"
            icon="heroicon-o-eye"
        />
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        Sent {{ $record->deliver_at->diffForHumans() }}
    </time>
    <div
        class="my-4 rounded-lg border-2 border-gray-300 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400">
        @if (!blank($record->subject))
            <div class="mb-2 flex flex-col">
                <p class="text-xs text-gray-400 dark:text-gray-500">Subject:</p>
                <p>{{ $record->subject }}</p>
            </div>
        @endif
        <div class="flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">Body:</p>
            <p>{{ $record->body }}</p>
        </div>
    </div>
</div>
