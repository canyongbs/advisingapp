{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
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
@if ($timelineRecords->count() < 1)
    <x-timeline::empty-state :message="$emptyStateMessage" />
@else
    <ol class="relative px-2">
        @foreach ($timelineRecords as $record)
            <li
                class="relative -left-6 mb-10 ml-10 w-full rounded-lg p-4 hover:bg-gray-50 md:ml-6 hover:dark:bg-gray-800"
            >
                @if (! $loop->last)
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
                            </x-slot>
                        </x-dynamic-component>
                    @else
                        <x-timeline::timeline-record :record="$record->timelineable">
                            <x-slot:view-record-icon>
                                <x-filament::icon-button
                                    class="absolute right-2 top-2"
                                    wire:click="viewRecord('{{ $record->timelineable->getKey() }}', '{{ $record->timelineable->getMorphClass() }}')"
                                    icon="heroicon-o-eye"
                                />
                            </x-slot>
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
                <hr class="flex-grow border-t border-dashed border-gray-200 dark:border-gray-700" />
                <span class="mt-1 text-sm text-gray-900 dark:text-white">{{ $noMoreRecordsMessage }}</span>
                <hr class="flex-grow border-t border-dashed border-gray-200 dark:border-gray-700" />
            </div>
        @endif
    </ol>
@endif
