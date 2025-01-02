<!--
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
-->
<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    context: Object,
});

const min = ref(1);
const max = ref(5);
const value = ref(Math.ceil(max.value / 2));

props.context.node.input(value.value);

watch(value, (value) => {
    props.context.node.input(value);
});
</script>

<template>
    <div class="grid gap-y-2 max-w-md">
        <div class="flex gap-4 items-end">
            <div class="w-full">
                <div class="flex justify-between text-sm">
                    <span>{{ min }}</span>
                    <span>{{ max }}</span>
                </div>
                <input class="w-full accent-primary-500" type="range" :min="min" :max="max" v-model="value" />
            </div>
            <div
                class="flex items-center max-w-md ring-1 ring-gray-400 focus-within:ring-primary-500 focus-within:ring-2 rounded"
            >
                <input
                    class="appearance-none bg-transparent focus:outline-none focus:ring-0 focus:shadow-none font-sans w-full px-3 py-2 border-none text-sm text-gray-700 placeholder-gray-400"
                    type="number"
                    :min="min"
                    :max="max"
                    v-model="value"
                />
            </div>
        </div>
    </div>
</template>
