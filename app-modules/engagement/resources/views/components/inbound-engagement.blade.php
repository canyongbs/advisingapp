@php
    use App\Filament\Resources\UserResource;
    use Assist\Engagement\Enums\EngagementDeliveryMethod;
    use Assist\AssistDataModel\Filament\Resources\StudentResource;
@endphp

<div class="flex-auto flex-auto rounded-md rounded-md bg-gray-100 p-3 p-3 dark:bg-gray-800">
    <div class="flex justify-between gap-x-4">
        <div class="py-0.5 text-xs leading-5 text-gray-500"><span class="font-medium text-gray-900">
                <a
                    class="font-medium text-green-300 underline hover:text-green-400"
                    href="{{ StudentResource::getUrl('view', ['record' => $engagement->sender]) }}"
                >
                    {{ $engagement->sender->full_name }}
                </a>
        </div>
        <x-filament::icon-button
            wire:click="viewEngagementResponse('{{ $engagement->id }}')"
            icon="heroicon-o-eye"
        />
    </div>
    <p class="flex-none py-0.5 text-xs leading-5 text-gray-100">
        Sent {{ $engagement->sent_at->diffForHumans() }}
    </p>
    <p class="mt-4 text-sm">{{ $engagement->content }}</p>
</div>
