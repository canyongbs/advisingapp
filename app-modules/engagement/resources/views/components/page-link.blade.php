<span wire:key="paginator-{{ $pageName }}-page{{ $page }}">
    @if ($page == $currentPage)
        <span aria-current="page">
            <span
                class="relative -ml-px inline-flex cursor-default items-center bg-transparent px-4 py-2 text-sm font-medium leading-5 text-primary-500 ring-1 ring-inset ring-gray-200 hover:bg-gray-50 dark:ring-gray-700 hover:dark:bg-gray-700"
            >{{ $page }}</span>
        </span>
    @else
        <button
            class="relative -ml-px inline-flex cursor-pointer items-center bg-transparent px-4 py-2 text-sm font-medium leading-5 text-gray-500 ring-1 ring-inset ring-gray-200 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 hover:dark:bg-gray-700"
            type="button"
            aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
            wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
        >
            {{ $page }}
        </button>
    @endif
</span>
