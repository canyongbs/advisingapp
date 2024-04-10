@php
    use AdvisingApp\Alert\Histories\AlertHistory;
@endphp

@php
    /* @var AlertHistory $record */
@endphp
<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">
            <div class="font-medium">
                Alert Created
            </div>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        {{ $record->created_at->diffForHumans() }}
    </time>

    <div class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal dark:border-gray-800 text-gray-400 dark:text-gray-500">
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['status']['key'] }}</p>
            <div class="prose dark:prose-invert">
                <p>{{ $record->formatted['status']['new'] }}</p>
            </div>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['severity']['key'] }}</p>
            <div class="prose dark:prose-invert">
                <p>{{ $record->formatted['severity']['new'] }}</p>
            </div>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['description']['key'] }}</p>
            <div class="prose dark:prose-invert">
                {{ str($record->formatted['description']['new'])->markdown()->sanitizeHtml()->toHtmlString() }}
            </div>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['suggested_intervention']['key'] }}</p>
            <div class="prose dark:prose-invert">
                {{ str($record->formatted['suggested_intervention']['new'])->markdown()->sanitizeHtml()->toHtmlString() }}
            </div>
        </div>
    </div>
</div>
