@if ($aggregateRecords->count() < 1)
    <x-timeline::empty-state :message="$emptyStateMessage" />
@else
    <ol class="relative">
        @foreach ($aggregateRecords as $record)
            <li
                class="relative -left-6 mb-10 ml-10 w-full rounded-lg p-4 hover:bg-gray-200 hover:dark:bg-gray-800 md:ml-6">
                @if (!$loop->last)
                    <div class="absolute -bottom-12 -left-3 top-3 flex w-6 justify-center">
                        <div class="w-px bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                @endif
                <span
                    class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-gray-300 ring-8 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800"
                >
                    <x-filament::icon
                        class="h-4 w-4 text-gray-800 dark:text-gray-100"
                        icon="{{ $record->timeline()->icon() }}"
                    />
                </span>

                <div class="ml-2">
                    @if ($record->timeline()->providesCustomView())
                        <x-dynamic-component
                            :component="$record->timeline()->renderCustomView()"
                            :record="$record"
                        >
                            <x-slot:view-record-icon>
                                <x-filament::icon-button
                                    class="fi-primary-color absolute right-2 top-2"
                                    wire:click="viewRecord('{{ $record->id }}', '{{ $record->getMorphClass() }}')"
                                    icon="heroicon-o-eye"
                                />
                            </x-slot:view-record-icon>
                        </x-dynamic-component>
                    @else
                        <x-timeline::timeline-record :record="$record">
                            <x-slot:view-record-icon>
                                <x-filament::icon-button
                                    class="absolute right-2 top-2"
                                    wire:click="viewRecord('{{ $record->id }}', '{{ $record->getMorphClass() }}')"
                                    icon="heroicon-o-eye"
                                />
                            </x-slot:view-record-icon>
                        </x-timeline::timeline-record>
                    @endif
                </div>

            </li>
        @endforeach
    </ol>
@endif
