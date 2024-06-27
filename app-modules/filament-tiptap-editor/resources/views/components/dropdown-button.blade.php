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
