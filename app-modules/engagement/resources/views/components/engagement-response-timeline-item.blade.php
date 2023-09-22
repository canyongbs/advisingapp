@php
    use Assist\AssistDataModel\Filament\Resources\StudentResource;
    use Assist\Engagement\Enums\EngagementDeliveryMethod;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-900 dark:text-white">
            {{-- TODO We need to figure out whether this is a student/prospect --}}
            <a
                class="font-medium underline"
                href="{{ StudentResource::getUrl('view', ['record' => $record->sender]) }}"
            >
                {{ $record->sender->full_name }}
            </a>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>

    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        Sent {{ $record->sent_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-300 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400">
        <div class="flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">Content:</p>
            <p>{{ $record->content }}</p>
        </div>
    </div>
</div>
