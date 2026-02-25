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
    use Filament\Facades\Filament;
@endphp

<x-layout>
    <section>
        <div class="mx-auto max-w-screen-xl px-4 py-8 lg:px-6 lg:py-16">
            <div class="mx-auto text-center">
                <img
                    class="mx-auto mb-3 max-w-md"
                    src="{{ Vite::asset('resources/svg/canyongbs-500.svg') }}"
                    alt="Internal Server Error"
                />
                <h1 class="text-primary-600 dark:text-primary-500 mb-3 text-5xl font-extrabold">
                    Not all who wander are lost...
                </h1>
                <p class="mb-5 font-bold tracking-tight text-gray-900 md:text-xl dark:text-gray-400">
                    But you might be. We can't find the page you're looking for.
                </p>
            </div>
        </div>
    </section>
</x-layout>
