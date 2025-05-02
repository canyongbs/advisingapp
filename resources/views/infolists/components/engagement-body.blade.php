<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="prose
    prose-h1:my-4
    prose-h2:my-4
    prose-h3:my-4
    prose-h4:my-4
    prose-h5:my-4
    prose-h6:my-4
    prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl prose-h4:text-lg prose-h5:text-base prose-h6:text-sm
    prose-h1:font-bold prose-h5:font-medium prose-h6:font-medium
    prose-h5:text-[--tw-prose-headings] prose-h6:text-[--tw-prose-headings]
    prose-hr:mt-4 prose-hr:mb-4
    dark:prose-invert"
>
    {{ $getState() }}
</div>

</x-dynamic-component>
