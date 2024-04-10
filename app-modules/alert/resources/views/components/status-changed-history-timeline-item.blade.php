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
                Alert Status Changed
            </div>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        {{ $record->created_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-400 dark:border-gray-800 dark:text-gray-500">
        Changed from
        <span class="prose font-semibold dark:prose-invert">{{ $record->formatted['status']['old'] }}</span>
        to
        <span class="prose font-semibold dark:prose-invert">{{ $record->formatted['status']['new'] }}</span>
    </div>
</div>
