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
<x-filament-panels::page
    class="flex h-full flex-col"
    full-height="true"
>
    <div
        class="h-full max-h-[calc(100vh-8rem)] w-full flex-1 flex-col rounded-lg border-0 border-gray-200 dark:border-gray-700 md:overflow-y-auto md:border">
        <div class="grid h-full grid-cols-1 md:grid-cols-8">
            @if ($loadingInbox)
                <x-filament::loading-indicator class="col-span-full h-12 w-12" />
            @else
                <x-engagement::message-center-inbox
                    :selectedEducatable="$recordModel"
                    :educatables="$educatables"
                />

                <x-engagement::message-center-content
                    :loadingTimeline="$loadingTimeline"
                    :educatable="$recordModel"
                    :timelineRecords="$timelineRecords"
                    :hasMorePages="$hasMorePages"
                    :emptyStateMessage="$emptyStateMessage"
                    :noMoreRecordsMessage="$noMoreRecordsMessage"
                />
            @endif
        </div>
    </div>
</x-filament-panels::page>
