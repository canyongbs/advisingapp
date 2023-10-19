@if ($timelineRecords->count() < 1)
    <x-timeline::empty-state :message="$emptyStateMessage" />
@else
    <ol class="relative">
        @foreach ($timelineRecords as $record)
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
                        icon="{{ $record->timelineable->timeline()->icon() }}"
                    />
                </span>

                <div class="ml-2">
                    @if ($record->timelineable()->timeline()->providesCustomView())
                        <x-dynamic-component
                            :component="$record->timelineable->timeline()->renderCustomView()"
                            :record="$record->timelineable"
                        >
                            <x-slot:view-record-icon>
                                <x-filament::icon-button
                                    class="absolute right-2 top-2"
                                    wire:click="viewRecord('{{ $record->timelineable->id }}', '{{ $record->timelineable->getMorphClass() }}')"
                                    icon="heroicon-o-eye"
                                />
                            </x-slot:view-record-icon>
                        </x-dynamic-component>
                    @else
                        <x-timeline::timeline-record :record="$record->timelineable">
                            <x-slot:view-record-icon>
                                <x-filament::icon-button
                                    class="absolute right-2 top-2"
                                    wire:click="viewRecord('{{ $record->timelineable->id }}', '{{ $record->timelineable->getMorphClass() }}')"
                                    icon="heroicon-o-eye"
                                />
                            </x-slot:view-record-icon>
                        </x-timeline::timeline-record>
                    @endif
                </div>

            </li>
        @endforeach
        <div
            x-data="{
                observe() {
                    let observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                @this.call('loadMoreRecords')
                            }
                        })
                    }, {
                        root: null
                    })
            
                    observer.observe(this.$el)
                }
            }"
            x-init="observe"
        ></div>
    </ol>
@endif
