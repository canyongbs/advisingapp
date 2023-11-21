{{--
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

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
                    <h1 class="ml-2">{{ $educatable->display_name }}</h1>
                    <x-filament::button wire:click="engage('{{ $educatable }}')">
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
