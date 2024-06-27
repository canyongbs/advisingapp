@props([
    'source' => null,
    'width' => null,
    'height' => null,
    'alt' => '',
])

<div
    class="fi-input-wrp h-64 w-full overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-950/10 dark:bg-white/5 dark:ring-white/20">
    <img
        class="h-full w-full object-cover"
        src="{{ $source }}"
        alt="{{ $alt }}"
        width="{{ $width }}"
        height="{{ $height }}"
    />
</div>
