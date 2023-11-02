@php
    use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
    use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">

            <a
                class="font-medium underline"
                href="{{ ServiceRequestUpdateResource::getUrl('view', ['record' => $record]) }}"
            >
                Case Updated
            </a>
            <span class="ml-2 flex space-x-2">
                @if ($record->direction === ServiceRequestUpdateDirection::Inbound)
                    <x-filament::icon
                        class="h-5 w-5 text-gray-400 dark:text-gray-100"
                        icon="heroicon-o-arrow-down-tray"
                    />
                @else
                    <x-filament::icon
                        class="h-5 w-5 text-gray-400 dark:text-gray-100"
                        icon="heroicon-o-arrow-up-tray"
                    />
                @endif
            </span>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        {{ $record->created_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400">
        {{ $record->update }}
    </div>
</div>
