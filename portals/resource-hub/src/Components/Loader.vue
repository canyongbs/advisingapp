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
    const props = defineProps({
        loading: {
            type: Boolean,
            default: false,
        },
    });
</script>
<template>
    <div v-if="loading" class="flex flex-col gap-y-8">
        <div>
            <div class="h-6 bg-gray-300 rounded w-24 mb-3"></div>
            <div class="grid gap-4 lg:grid-cols-2">
                <div
                    v-for="n in 4"
                    :key="'active-skel-' + n"
                    class="p-7 bg-white text-sm shadow-md rounded-lg border border-gray-200 animate-pulse"
                >
                    <div class="h-4 bg-gray-300 rounded w-1/2 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/4 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                </div>
            </div>
        </div>

        <div>
            <div class="h-6 bg-gray-300 rounded w-24 mb-3"></div>
            <div class="grid gap-4 lg:grid-cols-2">
                <div
                    v-for="n in 4"
                    :key="'expired-skel-' + n"
                    class="p-7 bg-white text-sm shadow-md rounded-lg border border-gray-200 animate-pulse"
                >
                    <div class="h-4 bg-gray-300 rounded w-1/2 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/4 mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                </div>
            </div>
        </div>
    </div>
</template>
