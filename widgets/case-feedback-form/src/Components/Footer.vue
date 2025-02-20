<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    logo: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex w-full flex-col">
        <div class="mt-4 flex w-full justify-center">
            <img class="block h-5" :src="logo" alt="Advising App Logo" />
        </div>

        <div class="flex w-full justify-center pb-4 pt-2">
            <span class="w-11/12 text-center text-xs lg:w-3/4 xl:w-7/12">
                © 2016-{{ new Date().getFullYear() }} Canyon GBS LLC. All Rights Reserved. Canyon GBS™ and Advising
                App™ are trademarks of Canyon GBS LLC. For more information or inquiries, please visit our website at
                <a class="text-blue-600 underline dark:text-blue-400" href="https://canyongbs.com/"
                    >https://canyongbs.com/</a
                >.
            </span>
        </div>
    </div>
</template>

<style scoped></style>
