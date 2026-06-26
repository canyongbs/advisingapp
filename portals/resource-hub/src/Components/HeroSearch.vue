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
    import BaseBadge from '@common/BaseBadge.vue';
    import { MagnifyingGlassIcon, TagIcon } from '@heroicons/vue/20/solid';
    import { ref } from 'vue';

    defineProps({
        modelValue: {
            type: String,
            default: '',
        },
        tags: {
            type: Object,
            required: true,
        },
        selectedTags: {
            type: Array,
            required: true,
        },
    });

    const emit = defineEmits(['update:modelValue', 'toggle-tag']);

    const inputRef = ref(null);
    const tagsMode = ref('selected');

    defineExpose({
        focus: () => inputRef.value?.focus(),
    });
</script>

<template>
    <label for="search" class="sr-only">Search</label>
    <div
        class="rounded-lg bg-white/10 ring-1 ring-white/20 transition duration-75 focus-within:bg-white/15 focus-within:ring-2 focus-within:ring-white/40"
    >
        <div class="flex">
            <div class="flex items-center gap-x-3 ps-3 pe-2">
                <MagnifyingGlassIcon class="size-5 text-white/70" aria-hidden="true" />
            </div>
            <div class="min-w-0 flex-1">
                <input
                    ref="inputRef"
                    type="search"
                    :value="modelValue"
                    @input="emit('update:modelValue', $event.target.value)"
                    id="search"
                    autocomplete="off"
                    placeholder="Search for articles and categories"
                    class="block w-full appearance-none border-none bg-transparent ps-0 px-3 py-2 text-start text-sm leading-6 text-white placeholder:text-white/50 focus:placeholder:text-white/75 focus:ring-0 focus:outline-none [&::-webkit-search-cancel-button]:invert [&::-webkit-search-cancel-button]:brightness-0 [&::-webkit-search-cancel-button]:opacity-70 [&::-webkit-search-cancel-button]:hover:opacity-100"
                />
            </div>
        </div>

        <div v-if="tags.length > 0" class="flex flex-wrap items-center gap-2 border-t border-white/15 px-3 py-2">
            <button
                type="button"
                @click="tagsMode = tagsMode === 'selected' ? 'all' : 'selected'"
                class="inline-flex shrink-0 items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset transition duration-75"
                :class="
                    tagsMode === 'all'
                        ? 'bg-white/20 text-white ring-white/40'
                        : 'bg-white/10 text-white/70 ring-white/20 hover:bg-white/15 hover:text-white/90'
                "
            >
                <TagIcon class="size-3" aria-hidden="true" />
                <template v-if="tagsMode === 'selected' && selectedTags.length > 0">
                    Filter by tag · {{ selectedTags.length }}
                </template>
                <template v-else>Filter by tag</template>
            </button>

            <template v-if="tagsMode === 'selected'">
                <BaseBadge
                    v-for="tag in tags.filter((t) => selectedTags.includes(t.id))"
                    :key="tag.id"
                    tag="button"
                    color="transparent-white"
                    @click="emit('toggle-tag', tag.id)"
                >
                    {{ tag.name }}
                </BaseBadge>
            </template>

            <template v-else>
                <BaseBadge
                    v-for="tag in tags"
                    :key="tag.id"
                    tag="button"
                    color="transparent-white"
                    :class="selectedTags.includes(tag.id) ? '' : 'opacity-60 hover:opacity-90'"
                    @click="emit('toggle-tag', tag.id)"
                >
                    {{ tag.name }}
                </BaseBadge>
            </template>
        </div>
    </div>
</template>
