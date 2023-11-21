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
@php
    use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
    use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">

            <a
                class="font-medium underline"
                href="{{ ServiceRequestUpdateResource::getUrl('view', ['record' => $record]) }}"
            >
                Service Request Update
            </a>
            <span class="ml-2 flex space-x-2">
                <x-filament::badge>
                    @if ($record->internal === true)
                        Internal
                    @else
                        External
                    @endif
                </x-filament::badge>

                @if ($record->direction === ServiceRequestUpdateDirection::Inbound)
                    <x-filament::icon
                        class="h-5 w-5 text-gray-400 dark:text-gray-100"
                        icon="heroicon-o-arrow-down-tray"
                    />
                @else
                    <x-filament::icon
                        class="h-5 w-5 text-gray-400 dark:text-gray-100"
                        icon="heroicon-o-arrow-up-tray"
                    />
                @endif
            </span>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        {{ $record->created_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400">
        {{ $record->update }}
    </div>
</div>
