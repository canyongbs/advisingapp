{{--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
--}}
@props([
    'active' => null,
    'label' => null,
    'icon' => null,
    'indicator' => null,
    'list' => true,
    'scrollable' => false,
    'customIcon' => null,
])
<div
    class="relative"
    x-data="{
        indicator: () => {{ $indicator ?? 'null' }}
    }"
    x-on:close-panel="$refs.panel.close()"
>
    @if ($indicator)
        <div
            class="pointer-events-none absolute right-0 top-0 font-mono text-[0.625rem] text-gray-800 dark:text-gray-300"
            x-text="{{ $indicator }}"
            x-bind:class="{ 'hidden': !indicator() }"
        ></div>
    @endif

    <x-filament-tiptap-editor::button
        action="$refs.panel.toggle"
        :active="$active"
        :label="$label"
        :icon="$icon"
    >
        @if (!$icon)
            {!! $customIcon !!}
        @endif
    </x-filament-tiptap-editor::button>

    <div
        x-ref="panel"
        x-float.placement.bottom-middle.flip.offset.arrow="{
            arrow: {
              element: $refs.arrow
            }
        }"
        x-cloak
        @class([
            'tiptap-panel absolute z-30 bg-gray-100 dark:bg-gray-800 rounded-md shadow-md top-full',
            'overflow-y-scroll max-h-48' => !$active,
        ])
    >
        <div
            class="z-1 bg-inherit absolute h-2 w-2 rotate-45 transform"
            x-ref="arrow"
        ></div>
        @if ($list)
            <ul
                class="z-2 relative min-w-[144px] divide-y divide-gray-300 overflow-hidden rounded-md text-sm text-gray-800 dark:divide-gray-700 dark:text-white">
                {{ $slot }}
            </ul>
        @else
            <div class="z-2 relative flex items-center gap-1 p-1">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
