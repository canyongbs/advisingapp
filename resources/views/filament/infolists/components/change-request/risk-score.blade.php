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
@php
    use AdvisingApp\CaseManagement\Models\ChangeRequest;

    $classes = match (ChangeRequest::getColorBasedOnRisk($getState())) {
        'green' => 'border-green-500 bg-green-400/10 text-green-500 ring-green-500 dark:border-green-500 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-500',
        'yellow' => 'border-yellow-500 bg-yellow-400/10 text-yellow-500 ring-yellow-500 dark:border-yellow-500 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-500',
        'orange' => 'border-orange-500 bg-orange-400/10 text-orange-500 ring-orange-500 dark:border-orange-500 dark:bg-orange-400/10 dark:text-orange-500 dark:ring-orange-500',
        'red' => 'border-red-600 bg-red-400/10 text-red-600 ring-red-600 dark:border-red-600 dark:bg-red-400/10 dark:text-red-600 dark:ring-red-600',
        default => '',
    };
@endphp

<div>
    <div class="flex flex-col">
        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                Risk Score
            </span>
        </label>

        <div class="mt-1">
            <div class="{{ $classes }} mt-0 flex items-center justify-center rounded-xl border-2 p-4 text-lg">
                {{ $getState() }}
            </div>
        </div>
    </div>

</div>
