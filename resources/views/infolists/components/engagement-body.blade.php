<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="prose prose-h1:text-3xl prose-h1:text-3xl prose-h1:leading-[1.3] prose-h2:text-2xl prose-h2:mt-5 prose-h2:mb-5 prose-h2:leading-[1.1] prose-h3:text-xl prose-h3:font-bold prose-h3:leading-[1.6] prose-h3:mb-0 prose-h4:text-lg prose-h4:font-bold prose-h4:leading-[2.5] prose-h4:mt-2 prose-h4:mb-1 prose-h5:text-base prose-h5:font-bold prose-h5:leading-[1.5] prose-h5:mb-1 prose-h6:text-sm prose-h6:font-bold prose-h6:leading-[3] prose-h6:mb-1 prose-hr:mt-4 prose-hr:mb-4 dark:prose-invert">
        {{ $getState() }}
    </div>
</x-dynamic-component>
