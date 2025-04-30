<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="prose font-bold
    prose-h1:mt-4 prose-h1:mb-4
    prose-h2:mt-4 prose-h2:mb-4
    prose-h3:mt-4 prose-h3:mb-4
    prose-h4:mt-4 prose-h4:mb-4
    prose-h5:mt-4 prose-h5:mb-4
    prose-h6:mt-4 prose-h6:mb-4
    prose-h1:leading-[1.5] prose-h2:leading-[1.5] prose-h3:leading-[1.5]
    prose-h4:leading-[1.5] prose-h5:leading-[1.5] prose-h6:leading-[1.5]
    prose-h4:text-lg prose-h5:text-base prose-h6:text-sm
    prose-hr:mt-4 prose-hr:mb-4
    dark:prose-invert"
>
    {{ $getState() }}
</div>

</x-dynamic-component>
