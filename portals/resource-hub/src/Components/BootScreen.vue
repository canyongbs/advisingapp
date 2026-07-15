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
<!--
    Cold-boot loading screen.

    Unlike the rest of the portal, this screen must be fully styled from the very
    first paint — before the async Tailwind stylesheet (loaded via the `css-url`
    link) has arrived. It therefore relies only on inline styles and an SVG whose
    spin is driven by SMIL (`<animateTransform>`), so it needs no external CSS or
    keyframes and never renders unstyled / oversized.
-->
<script setup>
    defineProps({
        label: {
            type: String,
            default: 'Loading…',
        },
    });
</script>

<template>
    <div
        role="status"
        :aria-label="label"
        style="
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: #f9fafb;
            color: #374151;
            font-family: ui-sans-serif, system-ui, sans-serif;
        "
    >
        <!--
            Same spinner artwork as @common/LoadingSpinner.vue (which we can't reuse
            directly here — its Tailwind classes aren't styled until the async CSS
            loads). Sized like the common "md" spinner (20px), coloured via
            `currentColor`, and spun with SMIL so it needs no external CSS.
        -->
        <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            style="display: block; flex-shrink: 0"
        >
            <g>
                <path
                    clip-rule="evenodd"
                    d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                    fill-rule="evenodd"
                    fill="currentColor"
                    opacity="0.2"
                />
                <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="currentColor" />
                <animateTransform
                    attributeName="transform"
                    attributeType="XML"
                    type="rotate"
                    from="0 12 12"
                    to="360 12 12"
                    dur="1s"
                    repeatCount="indefinite"
                />
            </g>
        </svg>

        <span style="font-weight: 500; font-size: 0.875rem">{{ label }}</span>
    </div>
</template>
