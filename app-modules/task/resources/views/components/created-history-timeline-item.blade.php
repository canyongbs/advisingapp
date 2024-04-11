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
@php
    use AdvisingApp\Task\Histories\TaskHistory;
@endphp

@php
    /* @var TaskHistory $record */
@endphp
<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">
            <div class="font-medium">
                Task Created
            </div>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        {{ $record->created_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-400 dark:border-gray-800 dark:text-gray-500">
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['title']['key'] }}</p>
            <div class="prose dark:prose-invert">
                {{ str($record->formatted['title']['new'])->markdown()->sanitizeHtml()->toHtmlString() }}
            </div>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['description']['key'] }}</p>
            <div class="prose dark:prose-invert">
                {{ str($record->formatted['description']['new'])->markdown()->sanitizeHtml()->toHtmlString() }}
            </div>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['status']['key'] }}</p>
            <div class="prose dark:prose-invert">
                <p>{{ $record->formatted['status']['new'] }}</p>
            </div>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['due']['key'] }}</p>
            <div class="prose dark:prose-invert">
                <p>{{ $record->formatted['due']['new'] }}</p>
            </div>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['assigned_to']['key'] }}</p>
            <a
                class="hover:underline"
                href="{{ $record->formatted['assigned_to']['extra']['new']['link'] }}"
            >
                <div class="prose dark:prose-invert">
                    {{ $record->formatted['assigned_to']['new'] }}
                </div>
            </a>
        </div>
        <div class="mb-2 flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $record->formatted['created_by']['key'] }}</p>
            <a
                class="hover:underline"
                href="{{ $record->formatted['created_by']['extra']['new']['link'] }}"
            >
                <div class="prose dark:prose-invert">
                    {{ $record->formatted['created_by']['new'] }}
                </div>
            </a>
        </div>
    </div>
</div>
