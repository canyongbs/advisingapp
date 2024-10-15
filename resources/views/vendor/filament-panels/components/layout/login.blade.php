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

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="fi-layout flex h-full w-full flex-row-reverse overflow-x-clip">
        <div class="fi-main-ctn w-screen flex flex-col lg:h-screen md:h-auto">
            <div class="flex justify-center items-center w-full h-32 border-b border-gray-200 mb-4">
                <x-filament-panels::logo />
            </div>
            <main class="fi-main mx-auto flex justify-center items-center h-full w-full px-4 md:px-6 lg:px-8 max-w-screen-lg">
                {{ $slot }}
            </main>
            <div class="mt-3 mb-6 inline-block w-full">
                <x-footer class="footer" />
            </div>
        </div>
    </div>
</x-filament-panels::layout.base>
