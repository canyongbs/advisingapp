{{--
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}
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
