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
    import { computed } from 'vue';
    import BaseCard from './BaseCard.vue';

    const props = defineProps({
        label: {
            type: String,
            required: true,
        },
        value: {
            type: [Number, String],
            required: true,
        },
        tone: {
            type: String,
            default: 'neutral',
            validator: (v) => ['neutral', 'warning', 'success', 'danger'].includes(v),
        },
    });

    const toneConfig = computed(() => {
        const map = {
            neutral: { bg: 'bg-white', labelColor: 'text-gray-500', valueColor: 'text-gray-900' },
            warning: { bg: 'bg-orange-50', labelColor: 'text-orange-600', valueColor: 'text-orange-600' },
            success: { bg: 'bg-green-50', labelColor: 'text-green-600', valueColor: 'text-green-700' },
            danger: { bg: 'bg-red-50', labelColor: 'text-red-600', valueColor: 'text-red-600' },
        };
        return map[props.tone] ?? map.neutral;
    });
</script>

<template>
    <BaseCard :bg="toneConfig.bg">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="mb-1 text-xs font-semibold" :class="toneConfig.labelColor">{{ label }}</p>
                <p class="text-4xl font-semibold leading-none tabular-nums" :class="toneConfig.valueColor">
                    {{ value }}
                </p>
            </div>
            <slot name="icon" />
        </div>
    </BaseCard>
</template>
