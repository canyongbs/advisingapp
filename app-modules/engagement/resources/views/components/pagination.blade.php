<div>
    @if ($paginator->hasPages())
        <nav
            class="flex flex-col items-center border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
            role="navigation"
            aria-label="Pagination Navigation"
        >
            <div class="flex flex-1 justify-between sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span
                            class="relative inline-flex cursor-default select-none items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium leading-5 text-gray-500"
                        >
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button
                            class="focus:shadow-outline-blue relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out hover:text-gray-500 focus:border-blue-300 focus:outline-none active:bg-gray-100 active:text-gray-700"
                            type="button"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            wire:loading.attr="disabled"
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                        >
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button
                            class="focus:shadow-outline-blue relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out hover:text-gray-500 focus:border-blue-300 focus:outline-none active:bg-gray-100 active:text-gray-700"
                            type="button"
                            wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            wire:loading.attr="disabled"
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                        >
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span
                            class="relative ml-3 inline-flex cursor-default select-none items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium leading-5 text-gray-500"
                        >
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            <div class="hidden flex-col-reverse sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="mt-2 text-xs leading-5 text-gray-400 dark:text-gray-500">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex rounded-md shadow-sm">
                        <span>
                            {{-- Previous Page Link --}}
                            @if ($paginator->onFirstPage())
                                <span
                                    aria-disabled="true"
                                    aria-label="{{ __('pagination.previous') }}"
                                >
                                    <span
                                        class="relative -ml-px inline-flex cursor-default items-center rounded-l-md bg-gray-100 px-2 py-2 text-sm font-medium leading-5 text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
                                        aria-hidden="true"
                                    >
                                        <x-filament::icon
                                            class="h-5 w-5 text-gray-400 dark:text-gray-100"
                                            icon="heroicon-o-chevron-left"
                                        />
                                    </span>
                                </span>
                            @else
                                <button
                                    class="relative -ml-px inline-flex cursor-pointer items-center rounded-l-md bg-gray-100 px-2 py-2 text-sm font-medium leading-5 text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
                                    type="button"
                                    aria-label="{{ __('pagination.previous') }}"
                                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                    dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                                    rel="prev"
                                >
                                    <x-filament::icon
                                        class="h-5 w-5 text-gray-400 dark:text-gray-100"
                                        icon="heroicon-o-chevron-left"
                                    />
                                </button>
                            @endif
                        </span>

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span aria-disabled="true">
                                    <span
                                        class="relative -ml-px inline-flex cursor-pointer items-center bg-gray-100 px-2 py-2 text-sm font-medium leading-5 text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
                                    >{{ $element }}</span>
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span
                                        wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page">
                                                <span
                                                    class="relative -ml-px inline-flex cursor-default items-center bg-gray-100 px-2 px-4 py-2 py-2 text-sm font-medium leading-5 text-primary-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:ring-gray-900"
                                                >{{ $page }}</span>
                                            </span>
                                        @else
                                            <button
                                                class="relative -ml-px inline-flex cursor-pointer items-center bg-gray-100 px-2 px-4 py-2 py-2 text-sm font-medium leading-5 text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
                                                type="button"
                                                aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            >
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        <span>
                            {{-- Next Page Link --}}
                            @if ($paginator->hasMorePages())
                                <button
                                    class="relative -ml-px inline-flex cursor-pointer items-center rounded-r-md bg-gray-100 px-2 py-2 text-sm font-medium leading-5 text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
                                    type="button"
                                    aria-label="{{ __('pagination.next') }}"
                                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                    dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                                    rel="next"
                                >
                                    <x-filament::icon
                                        class="h-5 w-5 text-gray-400 dark:text-gray-100"
                                        icon="heroicon-o-chevron-right"
                                    />
                                </button>
                            @else
                                <span
                                    aria-disabled="true"
                                    aria-label="{{ __('pagination.next') }}"
                                >
                                    <span
                                        class="relative -ml-px inline-flex cursor-default items-center rounded-r-md bg-gray-100 px-2 py-2 text-sm font-medium leading-5 text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-900"
                                        aria-hidden="true"
                                    >
                                        <x-filament::icon
                                            class="h-5 w-5 text-gray-400 dark:text-gray-100"
                                            icon="heroicon-o-chevron-right"
                                        />
                                    </span>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
