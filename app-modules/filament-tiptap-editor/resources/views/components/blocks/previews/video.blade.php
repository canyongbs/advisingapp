<div @class([
    'responsive' => $responsive,
])>
    <iframe
        src="{{ $url }}"
        style="aspect-ratio:{{ $width }}/{{ $height }}; width: 100%; height: auto;"
        width="{{ $width }}"
        height="{{ $height }}"
    />
</div>
