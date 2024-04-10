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
                Alert Updated
            </div>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        {{ $record->updated_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-400 dark:border-gray-800 dark:text-gray-500">
        Here's what changed

        <ul class="list-inside list-disc">
            @foreach ($record->formatted as $value)
                <li>
                    <span class="prose font-semibold dark:prose-invert">{{ $value['key'] }}</span>
                    changed from
                    <span class="prose font-semibold dark:prose-invert">{{ $value['old'] }}</span>
                    to
                    <span class="prose font-semibold dark:prose-invert">{{ $value['new'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
