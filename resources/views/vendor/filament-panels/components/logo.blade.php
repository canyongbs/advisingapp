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
    use AdvisingApp\Theme\Settings\ThemeSettings;
    use Illuminate\Support\Facades\Vite;

    $themeSettings = app(ThemeSettings::class);

    $settingsProperty = $themeSettings::getSettingsPropertyModel('theme.is_logo_active');
    $logo = $settingsProperty->getFirstMedia('logo');
    $darkLogo = $settingsProperty->getFirstMedia('dark_logo');
@endphp

@if ($themeSettings->is_logo_active && $logo)
    @php
        $logoUrl = $logo->getTemporaryUrl(
            expiration: now()->addMinutes(5),
            conversionName: $logo->hasGeneratedConversion('logo-height-250px') ? 'logo-height-250px' : '',
        );
    @endphp
    <img
        src="{{ $logoUrl }}"
        alt="{{ config('app.name') }}"
        @class([
            'h-full max-h-9',
            'w-full' => $logo->mime_type == 'image/svg+xml',
            'max-w-full' => $logo->mime_type != 'image/svg+xml',
            'dark:hidden' => $darkLogo,
        ])
    />

    @if ($darkLogo)
        @php
            $darkLogoUrl = $darkLogo->getTemporaryUrl(
                expiration: now()->addMinutes(5),
                conversionName: $darkLogo->hasGeneratedConversion('logo-height-250px') ? 'logo-height-250px' : '',
            );
        @endphp

        <img
            src="{{ $darkLogoUrl }}"
            alt="{{ config('app.name') }}"
            @class([
            'h-full max-h-9',
            'w-full' => $darkLogo->mime_type == 'image/svg+xml',
            'max-w-full' => $darkLogo->mime_type != 'image/svg+xml',
            'hidden dark:block',
        ])
        />
    @endif
@else
    <img
        src="{{ Vite::asset('resources/images/default-logo-light-201124.svg') }}"
        class="h-full max-h-9 max-w-full min-h-8 dark:hidden block"

    />

    <img
        src="{{ Vite::asset('resources/images/default-logo-dark-201124.svg') }}"
        class="h-full max-h-9 max-w-full min-h-8 hidden dark:block"
    />
@endif
