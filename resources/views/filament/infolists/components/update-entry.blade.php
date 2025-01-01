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

<div class="relative overflow-x-auto rounded-lg">
    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400 rtl:text-right">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th
                    class="px-6 py-3"
                    scope="col"
                >
                    Value
                </th>
                <th
                    class="px-6 py-3"
                    scope="col"
                >
                    Old
                </th>
                <th
                    class="px-6 py-3"
                    scope="col"
                >
                    New
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getState() as $change)
                <tr class="border-b bg-white dark:border-gray-700 dark:bg-gray-800">
                    <th
                        class="whitespace-nowrap px-6 py-4 font-medium text-gray-900 dark:text-white"
                        scope="row"
                    >
                        {{ $change['key'] }}
                    </th>
                    <td class="px-6 py-4">
                        {{ isset($change['old']) ? (is_array($change['old']) ? json_encode($change['old']) : $change['old']) : '' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ isset($change['new']) ? (is_array($change['new']) ? json_encode($change['new']) : $change['new']) : '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
