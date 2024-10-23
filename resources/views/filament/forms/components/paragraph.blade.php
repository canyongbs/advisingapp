<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :state-path="$getStatePath()"
>
    <p {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['text-sm text-gray-950 dark:text-white']) }}>
        {{ $getContent() }}
    </p>
</x-dynamic-component>
