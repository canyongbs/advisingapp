{{--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
    of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
    same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
    committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}
<div class="px-3 py-4 flex items-start gap-3">
    @if ($progress = $getState())
        <div class="grid gap-1">
            <div
                class="flex items-center w-56 h-6 rounded overflow-hidden shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 bg-gray-50 dark:bg-gray-950">
                <div class="bg-success-400 dark:bg-success-600 h-full"
                    style="width: {{ $progress->getSuccessfulPercentage() }}%"></div>
                <div class="bg-danger-400 dark:bg-danger-600 h-full" style="width: {{ $progress->getFailedPercentage() }}%">
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-normal">
                {{ number_format($progress->successful) }} of {{ number_format($progress->total) }} synced
                @if ($failed = $progress->getFailed()) & {{ number_format($failed) }} <x-filament::link
                :href="$progress->failedRowsCsvUrl" target="_blank" size="xs">failed</x-filament::link> @endif
            </p>
        </div>

        <div>
            <div class="mt-0.5 w-16 text-sm">
                {{ round($progress->getPercentage(), precision: 1) }}%
            </div>
        </div>
    @endif
</div>