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
    import { defineEmits, defineProps, ref } from 'vue';

    const props = defineProps({
        selectedFilter: {
            type: String,
            default: '',
        },
    });

    const emit = defineEmits(['change-filter']);
    const currentFilter = ref(props.selectedFilter);

    const updateFilter = (value) => {
        emit('change-filter', value);
    };
</script>
<template>
    <ul class="mb-5 flex list-none flex-row flex-wrap border-b-0 ps-0" role="tablist" data-twe-nav-ref>
        <li
            role="presentation"
            @click="updateFilter('all-articles')"
            class="text-sm font-semibold text-brand-600 px-4 py-3 hover:border-brand-600 cursor-pointer"
            :class="
                selectedFilter === '' || selectedFilter === 'all-articles'
                    ? 'border-b-2 border-brand-600'
                    : 'border-transparent'
            "
            aria-selected="true"
        >
            All Articles
        </li>
        <li
            role="presentation"
            @click="updateFilter('featured')"
            class="text-sm font-semibold text-brand-600 px-4 py-3 border-b-2 hover:border-brand-600 cursor-pointer"
            :class="selectedFilter === 'featured' ? 'border-b-2 border-brand-600' : 'border-transparent'"
            aria-selected="true"
        >
            Featured
        </li>
        <li
            role="presentation"
            @click="updateFilter('most-viewed')"
            class="text-sm font-semibold text-brand-600 px-4 py-3 border-b-2 hover:border-brand-600 cursor-pointer"
            :class="selectedFilter === 'most-viewed' ? 'border-b-2 border-brand-600' : 'border-transparent'"
            aria-selected="true"
        >
            Most Viewed
        </li>
    </ul>
</template>
