{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.
    
    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
    same in return. Canyon GBS® and Advising App® are registered trademarks of
    Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
<x-filament-panels::layout.base :livewire="$livewire">
    <div class="flex min-h-screen w-full flex-col bg-trout-50 dark:bg-gray-950">
        <div
            class="flex w-full flex-shrink-0 items-center justify-center border-b border-gray-200 p-4 dark:border-white/10"
        >
            <x-filament-panels::logo />
        </div>

        <div class="mx-auto flex w-full max-w-screen-lg flex-1 items-center justify-center px-4 py-8 md:px-6 lg:px-8">
            {{ $slot }}
        </div>

        <div class="mx-auto w-full max-w-screen-lg px-4 md:px-6 lg:px-8">
            <div
                class="flex w-full items-center justify-center border-t border-gray-200 pt-6 pb-4 dark:border-white/10"
            >
                <x-footer class="footer" />
            </div>
        </div>
    </div>
</x-filament-panels::layout.base>
