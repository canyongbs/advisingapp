<nav
    class="flex flex-col items-center justify-between rounded-bl-lg border-b-2 border-t-2 border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    aria-label="Pagination"
>
    <div class="flex flex-1 justify-between sm:justify-end">
        <a
            class="relative inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-offset-0 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
            href="{{ $educatables->previousPageUrl() }}"
        >Previous</a>
        <a
            class="relative ml-3 inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-offset-0 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
            href="{{ $educatables->nextPageUrl() }}"
        >Next</a>
    </div>
    <div class="mt-2 hidden sm:block">
        <p class="text-xs text-gray-400 dark:text-gray-500">
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
