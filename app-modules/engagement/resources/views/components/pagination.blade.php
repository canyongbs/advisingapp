<nav
    class="flex flex-col items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
    aria-label="Pagination"
>
    <div class="flex flex-1 justify-between sm:justify-end">
        <a
            class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-offset-0"
            href="{{ $educatables->previousPageUrl() }}"
        >Previous</a>
        <a
            class="relative ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-offset-0"
            href="{{ $educatables->nextPageUrl() }}"
        >Next</a>
    </div>
    <div class="mt-2 hidden sm:block">
        <p class="text-xs text-gray-700">
            Showing
            <span class="font-medium">{{ $educatables->firstItem() }}</span>
            to
            <span class="font-medium">{{ $educatables->lastItem() }}</span>
            of
            <span class="font-medium">{{ $educatables->total() }}</span>
            results
        </p>
    </div>
</nav>
