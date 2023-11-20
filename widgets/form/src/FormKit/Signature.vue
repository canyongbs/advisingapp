<script setup>
import { ref } from "vue";

const props = defineProps({
    context: Object,
});

const pad = ref(null);

const undo = () => {
    pad.value.undoSignature();
};

const clear = () => {
    pad.value.clearSignature();
};

const save = () => {
    const { data } = pad.value.saveSignature();

    props.context.node.input(data);
};

const resizeCanvas = () => {
    pad.value.resizeCanvas();
};
</script>

<template>
    <div class="flex flex-col gap-1">
        <VueSignaturePad
            width="350px"
            height="100px"
            ref="pad"
            :options="{ onBegin: resizeCanvas, onEnd: save }"
            class="border border-gray-400 rounded"
        />

        <div class="flex items-center gap-1">
            <button @click="undo" type="button" class="inline-flex items-center border border-gray-400 text-xs font-normal py-1 px-2 rounded focus-visible:outline-2 focus-visible:outline-blue-600 focus-visible:outline-offset-2">
                Undo
            </button>

            <button @click="clear" type="button" class="inline-flex items-center border border-gray-400 text-xs font-normal py-1 px-2 rounded focus-visible:outline-2 focus-visible:outline-blue-600 focus-visible:outline-offset-2">
                Clear
            </button>
        </div>
    </div>
</template>
