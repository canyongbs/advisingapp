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
<div class="flex items-start gap-3 px-3 py-4">
    @if ($progress = $getState())
        <div class="grid gap-1">
            <div
                class="flex h-6 w-56 items-center overflow-hidden rounded bg-gray-50 shadow-sm ring-1 ring-gray-950/10 dark:bg-gray-950 dark:ring-white/20">
                <div
                    class="h-full bg-success-400 dark:bg-success-600"
                    style="width: {{ $progress->getSuccessfulPercentage() }}%"
                ></div>
                <div
                    class="h-full bg-danger-400 dark:bg-danger-600"
                    style="width: {{ $progress->getFailedPercentage() }}%"
                >
                </div>
            </div>

            <p class="whitespace-normal text-xs text-gray-500 dark:text-gray-400">
                {{ number_format($progress->successful) }} of {{ number_format($progress->total) }} synced
                @if ($failed = $progress->getFailed())
                    & {{ number_format($failed) }} <x-filament::link
                        :href="$progress->failedRowsCsvUrl"
                        target="_blank"
                        size="xs"
                    >failed</x-filament::link>
                @endif
            </p>
        </div>

        <div>
            <div class="mt-0.5 w-16 text-sm">
                {{ round($progress->getPercentage(), precision: 1) }}%
            </div>
        </div>
    @endif
</div>
