<div>

    @if ($timelineRecords->count() < 1)
        <x-timeline::empty-state :message="$emptyStateMessage" />
    @else
        <div class="rounded-xl border bg-white">
            <div class="flex justify-between border-b px-6 py-4 text-lg font-medium text-black">
                Activity Feed
                <x-filament::button wire:click="openFullFeedModal">
                    Full Feed
                </x-filament::button>
            </div>
            <div class="text-lg font-medium text-black">
            <ol class="px-2 pl-8 pt-5">
                @foreach ($timelineRecords as $record)
                <li
                    class="relative -left-6 mb-3 ml-10 w-full rounded-lg p-4 hover:bg-gray-200 hover:dark:bg-gray-800 md:ml-6">
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
                        <div class="flex flex-row justify-between">
                            <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">
                                <a
                                    class="font-medium underline"
                                    href=""
                                >
                                @if ($record->timelineable()->timeline()->providesCustomView())
                                    {{ $this->fetchTitle($record->timelineable->getMorphClass(),$record->timelineable->getKey()) }}
                                @else
                                    {{ $record->timelineable?->timelineRecordTitle() }}
                                @endif
                                </a>
                            </h3>
                        </div>
                        <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ $record->timelineable?->created_at?->diffForHumans() }}
                        </time>
                    </div>
                </li>
                @endforeach
            </ol>
            {{-- modal section --}}
            <x-filament::modal id="show-full-feed" width="5xl" :close-button="true">
                <x-slot name="heading">
                    Timeline
                </x-slot>
                <ol class="relative px-2 pl-8 pt-5">
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
                                                wire:click="viewRecord('{{ $record->timelineable->getKey() }}', '{{ $record->timelineable->getMorphClass() }}')"
                                                icon="heroicon-o-eye"
                                            />
                                        </x-slot:view-record-icon>
                                    </x-dynamic-component>
                                @else
                                    <x-timeline::timeline-record :record="$record->timelineable">
                                        <x-slot:view-record-icon>
                                            <x-filament::icon-button
                                                class="absolute right-2 top-2"
                                                wire:click="viewRecord('{{ $record->timelineable->getKey() }}', '{{ $record->timelineable->getMorphClass() }}')"
                                                icon="heroicon-o-eye"
                                            />
                                        </x-slot:view-record-icon>
                                    </x-timeline::timeline-record>
                                @endif
                            </div>

                        </li>
                    @endforeach
                    @if ($hasMorePages === true)
                        <div
                            x-data="{
                                observe() {
                                    let observer = new IntersectionObserver((entries) => {
                                        entries.forEach(entry => {
                                            if (entry.isIntersecting) {
                                                @this.call('loadTimelineRecords')
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
                    @else
                        <div class="my-4 flex flex-row items-center gap-x-4">
                            <hr class="flex-grow border-t border-dashed border-gray-200 dark:border-gray-700">
                            <span class="mt-1 text-sm text-gray-900 dark:text-white">{{ $noMoreRecordsMessage }}</span>
                            <hr class="flex-grow border-t border-dashed border-gray-200 dark:border-gray-700">
                        </div>
                    @endif
                </ol>
                <x-slot name="footerActions">
                    <x-filament::button color="gray" wire:click="closeFullFeedModal">
                        Close
                    </x-filament::button>
                </x-slot>
            </x-filament::modal>
            </div>
        </div>
    @endif
</div>
