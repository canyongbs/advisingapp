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
    $mountedFormComponentActionsData = $getLivewire()->mountedFormComponentActionsData;

    $data = $mountedFormComponentActionsData[array_key_last($mountedFormComponentActionsData)];
@endphp

<div class="rounded-lg bg-gray-100 p-4 dark:bg-gray-950">
    <div
        class="grid gap-4"
        style="grid-template-columns: repeat({{ $data['columns'] }}, minmax(0, 1fr))"
    >
        @if ($data['asymmetric'])
            <div
                class="rounded-lg border border-dashed border-white bg-gray-300 p-0.5 text-center dark:border-gray-600 dark:bg-gray-800"
                style="grid-column: span {{ $data['asymmetric_left'] }};"
            >
                <p>1</p>
            </div>
            <div
                class="rounded-lg border border-dashed border-white bg-gray-300 p-0.5 text-center dark:border-gray-600 dark:bg-gray-800"
                style="grid-column: span {{ $data['asymmetric_right'] }};"
            >
                <p>1</p>
            </div>
        @else
            @foreach (range(1, $data['columns']) as $column)
                <div
                    class="rounded-lg border border-dashed border-white bg-gray-300 p-0.5 text-center dark:border-gray-600 dark:bg-gray-800">
                    <p>{{ $column }}</p>
                </div>
            @endforeach
        @endif
    </div>
</div>
