{{--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
<div
    class="col-span-full mt-2 h-full overflow-y-auto rounded-l-lg rounded-r-lg bg-white dark:border-gray-700 dark:bg-gray-900 md:col-span-5 md:mt-0 md:rounded-l-none md:border-l">
    @if ($loadingTimeline)
        <x-filament::loading-indicator class="h-12 w-12" />
    @else
        @if (is_null($educatable))
            <div class="p-4">
                <x-engagement::empty-state />
            </div>
        @else
            <div
                class="max-h-full overflow-y-auto"
                id="message-center-content"
            >
                <div
                    class="sticky top-0 z-[5] flex h-12 w-full items-center justify-between bg-gray-100 px-4 dark:bg-gray-700">
                    <h1 class="ml-2"> <a
                            href="{{ $educatable->filamentResource()::getUrl('view', ['record' => $educatable->identifier()]) }}"
                        >{{ $educatable->display_name }} </a></h1>
                    <x-filament::button wire:click="engage('{{ $educatable->identifier() }}')">
                        Engage
                    </x-filament::button>
                </div>
                <div class="p-6">
                    <x-timeline::timeline
                        :timelineRecords="$timelineRecords"
                        :hasMorePages="$hasMorePages"
                        :emptyStateMessage="$emptyStateMessage"
                        :noMoreRecordsMessage="$noMoreRecordsMessage"
                    />
                </div>
            </div>
        @endif
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('scroll-to-top', (event) => {
            var el = document.getElementById('message-center-content');

            if (el) {
                el.scrollTop = 0;
            }
        });
    });
</script>
