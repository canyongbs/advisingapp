<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
>
    <h1
        {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl']) }}>
        {{ $getContent() }}
    </h1>
</x-dynamic-component>
