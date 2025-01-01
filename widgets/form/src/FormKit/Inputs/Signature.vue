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
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    context: Object,
});

onMounted(() => {
    const fonts = document.createElement('link');
    fonts.type = 'text/css';
    fonts.rel = 'stylesheet';
    fonts.href = 'https://fonts.googleapis.com/css2?family=Satisfy&display=swap';

    document.head.appendChild(fonts);
});

const mode = ref('draw');

const drawingPad = ref(null);

const undoDrawing = () => {
    drawingPad.value.undoSignature();
};

const clearDrawing = () => {
    drawingPad.value.clearSignature();
};

const saveDrawing = () => {
    const { data } = drawingPad.value.saveSignature();

    props.context.node.input(data);
};

const resizeCanvas = () => {
    drawingPad.value.resizeCanvas();
};

const text = ref('');

watch(text, () => {
    const canvas = document.createElement('canvas');
    canvas.height = 100;
    canvas.width = 350;

    const canvasContext = canvas.getContext('2d');

    canvasContext.font = '30px Satisfy';
    canvasContext.fillText(text.value, 10, 50);

    props.context.node.input(canvas.toDataURL());
});
</script>

<template>
    <div class="flex flex-col gap-2">
        <RadioGroup v-model="mode">
            <RadioGroupLabel class="sr-only">Choose an input mode</RadioGroupLabel>

            <div class="flex items-center flex-wrap gap-2">
                <RadioGroupOption as="template" value="draw" v-slot="{ active, checked }">
                    <div
                        :class="[
                            active ? 'ring-2 ring-primary-600 ring-offset-2' : '',
                            checked
                                ? 'bg-primary-600 text-white hover:bg-primary-500'
                                : 'ring-1 ring-inset ring-gray-300 bg-white text-gray-900 hover:bg-gray-50',
                            'flex items-center justify-center rounded py-1 px-2 text-sm font-medium cursor-pointer focus:outline-none',
                        ]"
                    >
                        <RadioGroupLabel as="span">Draw it</RadioGroupLabel>
                    </div>
                </RadioGroupOption>

                <RadioGroupOption as="template" value="type" v-slot="{ active, checked }">
                    <div
                        :class="[
                            active ? 'ring-2 ring-primary-600 ring-offset-2' : '',
                            checked
                                ? 'bg-primary-600 text-white hover:bg-primary-500'
                                : 'ring-1 ring-inset ring-gray-300 bg-white text-gray-900 hover:bg-gray-50',
                            'flex items-center justify-center rounded py-1 px-2 text-sm font-medium cursor-pointer focus:outline-none',
                        ]"
                    >
                        <RadioGroupLabel as="span">Type it</RadioGroupLabel>
                    </div>
                </RadioGroupOption>
            </div>
        </RadioGroup>

        <div v-if="mode === 'draw'" class="flex flex-col gap-1">
            <VueSignaturePad
                width="350px"
                height="100px"
                ref="drawingPad"
                :options="{ onBegin: resizeCanvas, onEnd: saveDrawing }"
                class="border border-gray-400 rounded"
            />

            <div class="flex items-center gap-1">
                <button
                    @click="undoDrawing"
                    type="button"
                    class="inline-flex items-center border border-gray-400 text-xs font-normal py-1 px-2 rounded focus-visible:outline-2 focus-visible:outline-blue-600 focus-visible:outline-offset-2"
                >
                    Undo
                </button>

                <button
                    @click="clearDrawing"
                    type="button"
                    class="inline-flex items-center border border-gray-400 text-xs font-normal py-1 px-2 rounded focus-visible:outline-2 focus-visible:outline-blue-600 focus-visible:outline-offset-2"
                >
                    Clear
                </button>
            </div>
        </div>

        <div v-else-if="mode === 'type'" class="flex flex-col gap-1">
            <input
                type="text"
                v-model.lazy="text"
                class="text-3xl font-signature text-center border border-gray-400 rounded h-[100px] w-[350px]"
            />
        </div>
    </div>
</template>
