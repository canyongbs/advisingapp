<dt class="fi-in-entry-wrp-label inline-flex items-center gap-x-3">
    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
        {{ $getLabel() }}
    </span>
</dt>
<div class="mt-2 rounded border border-gray-700 p-4">
    <div class="prose mt-2 dark:prose-invert lg:prose-xl">
        {!! $getState() !!}
    </div>
</div>
