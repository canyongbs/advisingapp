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
@php
    use AdvisingApp\Authorization\Filament\Widgets\UnlicensedNotice;
    use AdvisingApp\Theme\Settings\ThemeSettings;
    use App\Filament\Widgets\Notifications;

    $themeSettings = app(ThemeSettings::class);

    $themeChangelogUrl = ! empty($themeSettings->changelog_url) ? $themeSettings->changelog_url : ThemeSettings::DEFAULT_CHANGELOG_URL;
    $productResourceHubUrl = ! empty($themeSettings->product_resource_hub_url) ? $themeSettings->product_resource_hub_url : ThemeSettings::DEFAULT_PRODUCT_RESOURCE_HUB_URL;
@endphp

<x-filament-panels::page class="@container">
    <div class="grid-cols-1 @5xl:grid-cols-2 grid gap-6">
        <div
            class="@container col-span-full flex flex-col rounded-xl bg-black bg-cover bg-no-repeat p-6 shadow-sm ring-1 ring-white/10"
            style="background-image: url('{{ asset('images/banner.png') }}')"
        >
            <div class="grid w-full gap-1 text-center md:text-start">
                <p class="text-2xl font-bold text-white">Welcome,</p>

                <p class="text-3xl font-bold text-white">{{ auth()->user()->name }}!</p>

                <p class="mt-2 text-sm text-gray-200" id="current-date"></p>

                <p class="text-sm text-gray-200" id="current-time"></p>
            </div>
        </div>

        @if (UnlicensedNotice::canView())
            @livewire(UnlicensedNotice::class)
        @else
            <x-version-card :theme-changelog-url="$themeChangelogUrl" />
            <x-resource-portal-card :product-resource-hub-url="$productResourceHubUrl" />
        @endif
    </div>

    @if (! UnlicensedNotice::canView())
        @livewire(Notifications::class)
    @endif
</x-filament-panels::page>

@script
    <script>
        (function () {
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', {
                timeZone: @js(config('app.timezone')),
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });

            function updateTime() {
                document.getElementById('current-time').textContent = new Date().toLocaleTimeString('en-US', {
                    timeZone: @js(config('app.timezone')),
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                });
            }

            updateTime();
            setInterval(updateTime, 1000);
        })();
    </script>
@endscript
