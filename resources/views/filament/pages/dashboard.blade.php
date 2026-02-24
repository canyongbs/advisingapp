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
    use AdvisingApp\Authorization\Filament\Widgets\UnlicensedNotice;
    use App\Filament\Widgets\Features;
    use App\Filament\Widgets\Notifications;
@endphp

<x-filament-panels::page>
    <div class="grid gap-6">
        <div
            class="col-span-full flex flex-col rounded-lg bg-black bg-cover bg-no-repeat px-16 py-8 lg:col-span-5"
            style="background-image: url('{{ asset('images/banner.png') }}')"
        >
            <div class="grid w-full gap-1 text-center md:text-start md:text-3xl">
                <div class="text-3xl font-bold text-white">Welcome,</div>

                <div class="text-4xl font-bold text-white">{{ auth()->user()->name }}!</div>

                <div class="text-xl text-gray-200">
                    <p id="current-date"></p>
                </div>

                <div class="text-xl text-gray-200">
                    <p id="current-time"></p>
                </div>
            </div>
        </div>

        <div class="col-span-full flex flex-col gap-3 lg:col-span-5">
            @if (UnlicensedNotice::canView())
                @livewire(UnlicensedNotice::class)
            @else
                @livewire(Features::class)

                @livewire(Notifications::class)
            @endif
        </div>
    </div>
</x-filament-panels::page>

<script>
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', {
        timeZone: @json($timezone),
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    document.getElementById('current-time').textContent = new Date().toLocaleTimeString('en-US', {
        timeZone: @json($timezone),
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
</script>
