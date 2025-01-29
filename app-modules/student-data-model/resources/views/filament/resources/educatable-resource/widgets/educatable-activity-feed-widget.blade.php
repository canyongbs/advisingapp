{{--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}
<x-filament-widgets::widget>
    <x-filament::section
        class="fi-section-has-subsections"
        wire:poll.10s=""
    >
        <x-slot name="heading">
            Activity Feed
        </x-slot>

        @if ($timelineRecords)
            <x-slot name="headerActions">
                <x-filament::button
                    color="gray"
                    :href="$viewUrl"
                    tag="a"
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
</x-filament-widgets::widget>
