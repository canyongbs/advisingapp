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
    import { ref, watch } from 'vue';

    const props = defineProps({
        context: Object,
    });

    const isChecked = ref(false);
    const textValue = ref('');

    function syncToFormKit() {
        props.context.node.input(isChecked.value && textValue.value.trim() ? textValue.value.trim() : null);
    }

    watch(isChecked, syncToFormKit);

    watch(textValue, (val) => {
        if (val.trim() && !isChecked.value) {
            isChecked.value = true;
        }
        syncToFormKit();
    });

    function onFocus() {
        isChecked.value = true;
    }
</script>

<template>
    <div class="flex items-center mb-1">
        <label class="flex items-center cursor-pointer select-none mr-2 flex-shrink-0">
            <input type="checkbox" v-model="isChecked" class="sr-only" />
            <div
                class="block relative h-5 w-5 rounded bg-white bg-gradient-to-b from-transparent to-gray-200 border flex-shrink-0 flex items-center justify-center"
                :class="isChecked ? 'border-primary-500' : 'border-gray-400'"
            >
                <svg v-if="isChecked" class="w-3 h-3 text-primary-500" viewBox="0 0 16 16" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z"
                    />
                </svg>
            </div>
        </label>

        <div
            class="flex items-center border rounded flex-1"
            :class="isChecked ? 'border-primary-500' : 'border-gray-400'"
        >
            <input
                type="text"
                v-model="textValue"
                @focus="onFocus"
                placeholder="Other"
                class="w-full px-3 py-2 border-none text-sm text-gray-700 placeholder-gray-400 bg-transparent focus:outline-none"
            />
        </div>
    </div>
</template>
