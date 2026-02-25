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
    use AdvisingApp\Prospect\Models\Prospect;
    use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
    use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;

    $educatabletype = $educatable::class === Prospect::class ? 'prospect' : 'student';
@endphp

<div
    class="z-10 flex max-w-md transform cursor-move flex-col rounded-lg bg-white p-5 shadow dark:bg-gray-800"
    data-pipeline="{{ $pipeline->getKey() }}"
    data-educatable="{{ $educatable->getKey() }}"
    wire:key="pipeline-{{ $pipeline->getKey() }}-{{ time() }}"
>
    <div class="flex items-center justify-between pb-4">
        <div class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $educatable?->full_name }}
            <br />
            <small>
                {{ str($pipeline->name)->limit(50) }}
            </small>
            <br />
            <small>
                {{ str($pipeline?->group?->name)->limit(50) }}
            </small>
        </div>
        @if ($educatabletype === 'prospect')
            <x-filament::icon-button
                href="{{ ProspectResource::getUrl('view', [
                    'record' => $educatable?->getKey(),
                ]) }}"
                icon="heroicon-m-arrow-top-right-on-square"
                tag="a"
                target="_blank"
                label="View Prospect"
            />
        @endif

        @if ($educatabletype === 'student')
            <x-filament::icon-button
                href="{{ StudentResource::getUrl('view', [
                    'record' => $educatable?->getKey(),
                ]) }}"
                icon="heroicon-m-arrow-top-right-on-square"
                tag="a"
                target="_blank"
                label="View Student"
            />
        @endif
    </div>
</div>
