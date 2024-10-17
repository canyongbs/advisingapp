<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :state-path="$getStatePath()"
>
    <h1
        {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['text-sm text-gray-950 dark:text-white']) }}>
        {{ $getContent() }}
    </h1>
</x-dynamic-component>
