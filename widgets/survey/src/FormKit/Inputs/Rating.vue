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

const min = ref(0);
const max = ref(10);
const value = ref(null);

props.context.node.input(value);

watch(value, (value) => {
    props.context.node.input(value);
});
</script>

<template>
    <div class="grid gap-y-2 w-full max-w-md">
        <div class="flex gap-4 items-end">
            <div class="w-full">
                <div class="flex justify-between text-sm">
                    <span>NOT AT ALL LIKELY</span>
                    <span>EXTREMELY LIKELY</span>
                </div>
                <div class="w-full grid grid-flow-col justify-stretch">
                    <button
                        class="ring-1 ring-gray-400 ring-inset rounded-l appearance-none bg-transparent w-full px-3 py-2 text-sm text-gray-700 placeholder-gray-400"
                        :class="{ 'ring-primary-500 ring-2': value === min }"
                        type="button"
                        @click="value = min"
                    >
                        {{ min }}
                    </button>
                    <button
                        v-for="number in max - 1"
                        class="ring-1 ring-gray-400 ring-inset appearance-none bg-transparent w-full px-3 py-2 text-sm text-gray-700 placeholder-gray-400"
                        :class="{ 'ring-primary-500 ring-2': value === number }"
                        type="button"
                        @click="value = number"
                    >
                        {{ number }}
                    </button>
                    <button
                        class="ring-1 ring-gray-400 ring-inset focus-within:ring-primary-500 focus-within:ring-2 rounded-r appearance-none bg-transparent w-full px-3 py-2 text-sm text-gray-700 placeholder-gray-400"
                        :class="{ 'ring-primary-500 ring-2': value === max }"
                        type="button"
                        @click="value = max"
                    >
                        {{ max }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
