<!--
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
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
