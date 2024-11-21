<x-filament-widgets::widget>
    <x-filament::section
        class="fi-section-has-subsections"
        wire:poll.10s
    >
        <x-slot name="heading">
            Activity Feed
        </x-slot>

        @if ($timelineRecords)
            <x-slot name="headerActions">
                <x-filament::button
                    x-on:click="$dispatch('open-modal', { id: 'feed' })"
                    color="gray"
                >
                    Full Feed
                </x-filament::button>
            </x-slot>
        @endif

        @forelse ($timelineRecords->slice(0, 5) as $record)
            <button
                class="flex w-full items-center gap-6 px-6 py-3 text-start"
                type="button"
                wire:click="viewRecord('{{ $record->timelineable->getKey() }}', '{{ $record->timelineable->getMorphClass() }}')"
            >
                <div class="shrink-0 rounded-full bg-blue-100 p-3 dark:bg-blue-500/20">
                    @svg($record->timelineable->timeline()->icon(), 'h-6 w-6 text-blue-500 dark:text-blue-400', ['wire:loading.remove', 'wire:target' => "viewRecord('{$record->timelineable->getKey()}', '{$record->timelineable->getMorphClass()}')"])

                    <x-filament::loading-indicator
                        class="h-6 w-6 text-blue-500 dark:text-blue-400"
                        wire:loading.delay.none
                        wire:target="viewRecord('{{ $record->timelineable->getKey() }}', '{{ $record->timelineable->getMorphClass() }}')"
                    />
                </div>

                <div class="grid flex-1 gap-1 whitespace-nowrap">
                    <div class="flex flex-wrap gap-x-3">
                        <div class="flex flex-1 flex-wrap items-end gap-x-3 gap-y-1">
                            <p class="font-medium text-gray-950 dark:text-white">
                                @if ($record->timelineable()->timeline()->providesCustomView())
                                    {{ $this->getTimelineRecordTitle($record->timelineable) }}
                                @else
                                    {{ $record->timelineable?->timelineRecordTitle() }}
                                @endif
                            </p>

                            <p class="mb-0.5 text-xs text-gray-500 dark:text-gray-400">
                                <span
                                    class="font-medium text-gray-950 dark:text-white">{{ $record->timelineable?->created_at?->toFormattedDateString() }}</span>
                                at {{ $record->timelineable?->created_at?->format('g:ia') }}
                            </p>
                        </div>

                        @if ($user = $this->getTimelineRecordUser($record->timelineable))
                            <div class="mt-0.5 shrink-0">
                                <x-filament::badge
                                    color="gray"
                                    size="sm"
                                >
                                    {{ $user->name }}
                                </x-filament::badge>
                            </div>
                        @endif
                    </div>

                    <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->getTimelineRecordDescription($record->timelineable) }}
                    </p>
                </div>
            </button>
        @empty
            <div class="p-6">
                <div class="mx-auto grid max-w-lg justify-items-center gap-4 text-center">
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        @svg('heroicon-o-inbox-arrow-down', 'h-6 w-6 text-gray-500 dark:text-gray-400')
                    </div>

                    <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No activities
                    </h4>
                </div>
            </div>
        @endforelse
    </x-filament::section>

    <x-filament::modal
        id="feed"
        width="5xl"
    >
        <x-slot name="heading">
            Timeline
        </x-slot>

        {{-- @todo: Refactor --}}
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
                                        this.$wire.loadTimelineRecords()
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
                    <span class="mt-1 text-sm text-gray-900 dark:text-white">You have reached the end of the engagement
                        timeline.</span>
                    <hr class="flex-grow border-t border-dashed border-gray-200 dark:border-gray-700">
                </div>
            @endif
        </ol>
        {{-- @endtodo --}}

        <x-slot name="footerActions">
            <x-filament::button
                color="gray"
                x-on:click="close"
            >
                Close
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
