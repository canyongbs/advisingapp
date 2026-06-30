<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
    import BaseTable from './BaseTable.vue';
    import BaseTableBody from './BaseTableBody.vue';
    import BaseTableHeader from './BaseTableHeader.vue';

    defineProps({
        columns: {
            type: Number,
            default: 4,
        },
        rows: {
            type: Number,
            default: 5,
        },
        label: {
            type: String,
            default: 'Loading, please wait…',
        },
    });
</script>

<template>
    <BaseTable role="status" aria-busy="true" class="animate-pulse">
        <BaseTableHeader>
            <tr>
                <th v-for="col in columns" :key="col" class="px-4 py-3">
                    <div class="h-3 rounded bg-gray-200" :class="col === 1 ? 'w-16' : 'w-20'"></div>
                </th>
            </tr>
        </BaseTableHeader>
        <BaseTableBody>
            <tr v-for="row in rows" :key="row">
                <td v-for="col in columns" :key="col" class="px-4 py-4">
                    <div
                        class="h-3 rounded"
                        :class="[
                            col === 1 ? 'w-40 bg-gray-200' : col % 3 === 0 ? 'w-16 bg-gray-100' : 'w-24 bg-gray-200',
                        ]"
                    ></div>
                    <div v-if="col === 1" class="mt-2 h-2.5 w-28 rounded bg-gray-100"></div>
                </td>
            </tr>
        </BaseTableBody>
        <span class="sr-only">{{ label }}</span>
    </BaseTable>
</template>
