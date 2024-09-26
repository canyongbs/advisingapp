@use('App\Filament\Resources\UserResource')
<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">
            <a
                class="font-medium underline"
                href="{{ UserResource::getUrl('view', ['record' => $record->user]) }}"
            >
                {{ $record->user->name }}
            </a>
            <span class="ml-2 flex space-x-2">
                <div class="relative">
                  <x-filament::icon
                      class="h-5 w-5 text-gray-400 dark:text-gray-100"
                      icon="heroicon-o-pencil-square"
                  />
                </div>
            </span>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        {{ $record?->type?->name }}
    </time>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        Sent {{ $record->created_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400">
        @if (!blank($record->subject))
            <div class="mb-2 flex flex-col">
                <p class="text-xs text-gray-400 dark:text-gray-500">Subject:</p>
                <p>{{ $record->subject }}</p>
            </div>
        @endif
        <div class="flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">Description:</p>
            <div class="prose dark:prose-invert">
                {{ $record->description }}
            </div>
        </div>
    </div>
</div>
