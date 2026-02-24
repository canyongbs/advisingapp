{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.
    
    Notice:
    
    - You may not provide the software to third parties as a hosted or managed
    service, where the service provides users with access to any substantial set of
    the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
    in the software, and you may not remove or obscure any functionality in the
    software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
    of the licensor in the software. Any use of the licensor’s trademarks is subject
    to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
    same in return. Canyon GBS™ and Advising App™ are registered trademarks of
    Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
@php
    use AdvisingApp\CaseManagement\Enums\CaseUpdateDirection;
    use AdvisingApp\CaseManagement\Filament\Resources\CaseUpdates\CaseUpdateResource;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">
            <a class="font-medium underline" href="{{ CaseUpdateResource::getUrl('view', ['record' => $record]) }}">
                Case Update Created
            </a>
            <span class="ml-2 flex space-x-2">
                <x-filament::badge>
                    @if ($record->internal === true)
                        Internal
                    @else
                        External
                    @endif
                </x-filament::badge>

                @if ($record->direction === CaseUpdateDirection::Inbound)
                    <x-filament::icon class="w-5 text-gray-400 dark:text-gray-100" icon="heroicon-o-arrow-down-tray" />
                @else
                    <x-filament::icon class="w-5 text-gray-400 dark:text-gray-100" icon="heroicon-o-arrow-up-tray" />
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
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400"
    >
        {{ $record->update }}
    </div>
</div>
