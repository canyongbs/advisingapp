{{--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
<div class="flex w-full flex-col items-start justify-start gap-4 sm:flex-row md:gap-8">
    @if ($this->getNameWords())
        <div class="">
            <span
                class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-500 text-2xl text-white">{{ $this->getNameWords() }}</span>
        </div>
    @endif
    <div class="gap-4">
        <h2 class="mb-3 text-3xl font-semibold text-black">{{ $record?->full_name }}</h2>
        <div class="mb-3 flex flex-wrap items-center gap-3 lg:gap-6">
            <div class="flex items-center gap-2">
                <x-icon
                    class="w-5"
                    name="heroicon-m-user"
                ></x-icon>
                <span class="font-medium">Student</span>
            </div>
            <div class="flex items-center gap-2">
                <x-icon
                    class="w-5"
                    name="heroicon-m-heart"
                ></x-icon>
                <span class="font-medium">Goes by "{{ $record?->preferred }}"</span>
            </div>
            <div class="flex items-center gap-2">
                <x-icon
                    class="w-5"
                    name="heroicon-m-phone"
                ></x-icon>
                <span class="font-medium">{{ $record?->phone }}</span>
            </div>
            <div class="flex items-center gap-2">
                <x-icon
                    class="w-5"
                    name="heroicon-m-envelope"
                ></x-icon>
                <span class="font-medium">{{ $record?->email }}</span>
            </div>
            <div class="flex items-center gap-2">
                <x-icon
                    class="w-5"
                    name="heroicon-m-building-library"
                ></x-icon>
                <span class="font-medium">{{ $record?->hsgrad }}</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
            @if ($record?->firstgen)
                <span class="border border-blue-500 px-6 py-1 text-sm font-medium text-blue-500">
                    First Gen
                </span>
            @endif
            @if ($record?->dual)
                <span class="border border-blue-500 px-6 py-1 text-sm font-medium text-blue-500">
                    Dual
                </span>
            @endif
            @if ($record?->sap)
                <span class="border border-blue-500 px-6 py-1 text-sm font-medium text-blue-500">
                    SAP
                </span>
            @endif
            @if ($record?->dfw)
                <span class="border border-blue-500 px-6 py-1 text-sm font-medium text-blue-500">
                    DFW {{ $record?->dfw }}
                </span>
            @endif
        </div>
    </div>
</div>
