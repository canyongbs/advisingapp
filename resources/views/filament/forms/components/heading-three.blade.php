<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
>
    <h3
        {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['text-xl font-semibold text-gray-950 dark:text-white sm:text-2xl']) }}>
        {{ $getContent() }}
    </h3>
</x-dynamic-component>
