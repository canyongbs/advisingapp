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
    import { onUnmounted, ref, watch } from 'vue';

    const props = defineProps({
        active: {
            type: Boolean,
            default: false,
        },
        // Only reveal the bar once a navigation has been pending for this long, so
        // instant (cache-hit) navigations — pagination, tab switches — don't flash it.
        delay: {
            type: Number,
            default: 120,
        },
    });

    const progress = ref(0);
    const visible = ref(false);

    let trickleTimer = null;
    let hideTimer = null;
    let showTimer = null;

    function clearShowTimer() {
        if (showTimer) {
            clearTimeout(showTimer);
            showTimer = null;
        }
    }

    function clearTimers() {
        if (trickleTimer) {
            clearInterval(trickleTimer);
            trickleTimer = null;
        }

        if (hideTimer) {
            clearTimeout(hideTimer);
            hideTimer = null;
        }

        clearShowTimer();
    }

    function start() {
        clearTimers();

        visible.value = true;
        progress.value = 8;

        trickleTimer = setInterval(() => {
            const remaining = 90 - progress.value;

            if (remaining <= 0.5) {
                return;
            }

            progress.value += Math.max(0.5, remaining * 0.1);
        }, 300);
    }

    function finish() {
        clearTimers();

        progress.value = 100;

        hideTimer = setTimeout(() => {
            visible.value = false;
            progress.value = 0;
        }, 300);
    }

    watch(
        () => props.active,
        (active) => {
            if (active) {
                clearShowTimer();
                showTimer = setTimeout(start, props.delay);
            } else if (visible.value) {
                finish();
            } else {
                // Became idle before the delay elapsed — never show the bar.
                clearShowTimer();
            }
        },
    );

    onUnmounted(clearTimers);
</script>

<template>
    <div v-show="visible" class="pointer-events-none fixed inset-x-0 top-0 z-9999 h-0.5" aria-hidden="true">
        <div
            class="h-full bg-[rgb(var(--primary-500))] shadow-[0_0_10px_rgba(var(--primary-500),0.5)] transition-[width,opacity] duration-300 ease-out"
            :class="progress >= 100 ? 'opacity-0' : 'opacity-100'"
            :style="{ width: progress + '%' }"
        ></div>
    </div>
</template>
