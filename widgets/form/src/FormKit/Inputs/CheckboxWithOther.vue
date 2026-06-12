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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
    import { ref, computed, watch } from 'vue';

    const props = defineProps({
        context: Object,
    });

    const fieldLabel = computed(() => props.context.fieldLabel || props.context.label || '');

    // Normalize options from FormKit format (array of {value, label} or flat object)
    const normalizedOptions = computed(() => {
        const opts = props.context.options || [];
        if (Array.isArray(opts)) {
            return opts.map((opt) => {
                if (typeof opt === 'object' && opt !== null) {
                    return { value: opt.value, label: opt.label };
                }
                return { value: opt, label: opt };
            });
        }
        // Object format { value: label }
        return Object.entries(opts).map(([value, label]) => ({ value, label }));
    });

    const selectedValues = ref([]);
    const otherChecked = ref(false);
    const otherText = ref('');

    function syncToFormKit() {
        const result = [...selectedValues.value];
        if (otherChecked.value && otherText.value.trim()) {
            result.push(otherText.value.trim());
        }
        props.context.node.input(result.length > 0 ? result : []);
    }

    watch(selectedValues, syncToFormKit, { deep: true });
    watch(otherChecked, syncToFormKit);
    watch(otherText, (val) => {
        if (val.trim() && !otherChecked.value) {
            otherChecked.value = true;
        }
        syncToFormKit();
    });
</script>

<template>
    <fieldset class="max-w-md border border-gray-400 rounded px-2 pb-1">
        <legend v-if="fieldLabel" class="font-bold text-sm">{{ fieldLabel }}</legend>

        <div
            v-for="option in normalizedOptions"
            :key="option.value"
            class="formkit-option"
        >
            <label class="flex items-center mb-1 cursor-pointer">
                <input
                    type="checkbox"
                    :value="option.value"
                    v-model="selectedValues"
                    class="absolute w-0 h-0 overflow-hidden opacity-0 pointer-events-none peer"
                />
                <span class="block relative h-5 w-5 mr-2 rounded bg-white bg-gradient-to-b from-transparent to-gray-200 border border-gray-400 peer-checked:border-primary-500 text-transparent peer-checked:text-primary-500">
                    <span class="flex p-[3px] w-full h-full absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                            <path fill-rule="evenodd" d="M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z" />
                        </svg>
                    </span>
                </span>
                <span class="text-sm text-gray-700 mt-1 select-none">{{ option.label }}</span>
            </label>
        </div>

        <div class="formkit-option">
            <label class="flex items-center mb-1 cursor-pointer">
                <input
                    type="checkbox"
                    v-model="otherChecked"
                    class="absolute w-0 h-0 overflow-hidden opacity-0 pointer-events-none peer"
                />
                <span class="block relative h-5 w-5 mr-2 rounded bg-white bg-gradient-to-b from-transparent to-gray-200 border border-gray-400 peer-checked:border-primary-500 text-transparent peer-checked:text-primary-500 flex-shrink-0">
                    <span class="flex p-[3px] w-full h-full absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                            <path fill-rule="evenodd" d="M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z" />
                        </svg>
                    </span>
                </span>
                <input
                    type="text"
                    v-model="otherText"
                    placeholder="Other"
                    @focus="otherChecked = true"
                    class="w-full px-3 py-1.5 border rounded text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-primary-500"
                    :class="otherChecked ? 'border-primary-500' : 'border-gray-400'"
                />
            </label>
        </div>
    </fieldset>
</template>
