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
<script setup></script>

<template>
    <div class="flex-1 bg-gray-50">
        <div class="bg-[linear-gradient(to_right_bottom,rgba(var(--primary-500),1),rgba(var(--primary-800),1))]">
            <div class="max-w-(--breakpoint-xl) flex flex-col gap-y-6 mx-auto px-6 py-12">
                <div class="flex flex-col gap-y-3 text-left">
                    <slot name="breadcrumbs" />

                    <div class="flex flex-col gap-y-2">
                        <h1 class="text-3xl font-bold text-white"><slot name="heading" /></h1>
                        <div v-if="$slots.description" class="text-sm text-white/75"><slot name="description" /></div>
                    </div>
                </div>

                <div v-if="$slots.belowHeaderContent">
                    <slot name="belowHeaderContent" />
                </div>
            </div>
        </div>

        <div class="max-w-(--breakpoint-xl) mx-auto px-6 py-8 flex flex-col gap-y-6">
            <slot />
        </div>
    </div>
</template>
