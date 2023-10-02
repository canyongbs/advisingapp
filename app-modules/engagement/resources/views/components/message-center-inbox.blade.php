@php
    use Carbon\Carbon;
@endphp

<div
    class="border-b-1 max-h-content w-full overflow-y-scroll rounded-l-lg border-l-2 border-r-2 border-t-2 border-gray-200 border-r-gray-50 bg-white dark:border-gray-700 dark:bg-gray-800 lg:w-1/3">
    <div
        class="block items-center rounded-tl-lg border-b-2 border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 sm:flex">
        <div class="flex items-center">
            <span class="font-bold text-gray-500 dark:text-gray-400 sm:text-xs md:text-sm">
                My Subscribed Engagements
            </span>
        </div>
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div class="min-w-full divide-y divide-gray-200">
                        <ul class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach ($educatables as $educatable)
                                <li
                                    @class([
                                        'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700',
                                        'bg-gray-100 dark:bg-gray-700' =>
                                            $selectedEducatable?->identifier() === $educatable->identifier(),
                                    ])
                                    wire:click="selectEducatable('{{ $educatable->identifier() }}', '{{ $educatable->getMorphClass() }}')"
                                >
                                    <div class="justify-left flex flex-col items-center whitespace-nowrap p-4">
                                        <div class="w-full text-base font-normal text-gray-700 dark:text-gray-400">
                                            {{ $educatable->display_name }}
                                        </div>
                                        <div class="w-full text-xs font-normal text-gray-700 dark:text-gray-400">
                                            Last engaged at

                                            {{ Carbon::parse($educatable->latest_activity)->format('g:ia - M j, Y') }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
