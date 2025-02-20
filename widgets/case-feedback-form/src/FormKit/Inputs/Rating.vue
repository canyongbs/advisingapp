<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    context: Object,
});

const min = ref(1);
const max = ref(5);
const value = ref(null);

watch(value, (value) => {
    props.context.node.input(value);
});
</script>

<template>
    <div class="grid gap-y-2 w-full max-w-md">
        <div class="flex gap-4 items-end">
            <div class="w-full">
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
                        v-for="number in max - 2"
                        class="ring-1 ring-gray-400 ring-inset appearance-none bg-transparent w-full px-3 py-2 text-sm text-gray-700 placeholder-gray-400"
                        :class="{ 'ring-primary-500 ring-2': value === number + 1 }"
                        type="button"
                        @click="value = number + 1"
                    >
                        {{ number + 1 }}
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
