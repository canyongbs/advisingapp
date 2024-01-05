<script setup>

import {ref, watch} from "vue";

const props = defineProps({
    context: Object,
});

const min = ref(1);
const max = ref(5);
const value = ref(Math.ceil(max.value / 2));

props.context.node.input(value);

watch(value, (value) => {
    props.context.node.input(value);
})

</script>

<template>
    <div class="grid gap-y-2">
        <div class="flex gap-4">
            <div class="w-full">
                <div class="flex justify-between">
                    <span>{{ min }}</span>
                    <span>{{ max }}</span>
                </div>
                <input class="w-full" type="range" :min=min :max=max v-model="value">
            </div>
            <div class="flex items-end">
                <input class="p-1 rounded" type="number" :min=min :max=max v-model="value">
            </div>
        </div>
    </div>
</template>
