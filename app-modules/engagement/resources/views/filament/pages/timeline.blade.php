<x-filament-panels::page>
    @if ($aggregateRecords->count() < 1)
        <x-engagement::empty-state :message="$emptyStateMessage" />
    @else
        <ol class="relative">
            @foreach ($aggregateRecords as $record)
                {{-- TODO Figure out how to ensure this doesn't show on the last record --}}
                {{-- We don't want a "carry over" line to a non existing item --}}
                @if (!$loop->last)
                    <div class="absolute -bottom-6 -left-3 top-0 z-10 flex w-6 justify-center">
                        <div class="w-px bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                @endif

                <li class="z-50 mb-10 ml-6">
                    <span
                        class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 ring-8 ring-white dark:bg-blue-900 dark:ring-gray-900"
                    >
                        <x-filament::icon
                            class="h-4 w-4 text-gray-800 dark:text-gray-100"
                            icon="{{ $record->icon() }}"
                        />
                    </span>

                    @if ($record->providesCustomView())
                        <x-dynamic-component
                            :component="$record->renderCustomView()"
                            :record="$record"
                        />
                    @else
                        <x-engagement::timeline-record :record="$record" />
                    @endif

                </li>
            @endforeach
        </ol>
    @endif
</x-filament-panels::page>
