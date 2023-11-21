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
    use App\Models\SettingsProperty;
    use Assist\Theme\Settings\ThemeSettings;
    use Illuminate\Support\Facades\Vite;

    $themeSettings = app(ThemeSettings::class);

    $settingsProperty = SettingsProperty::getInstance('theme.is_logo_active');
    $logo = $settingsProperty->getFirstMedia('logo');
    $darkLogo = $settingsProperty->getFirstMedia('dark_logo');
@endphp

@if ($themeSettings->is_logo_active && $logo)
    <img
        src="{{ $logo->getTemporaryUrl(
            expiration: now()->addMinutes(5),
            conversionName: 'logo-height-250px',
        ) }}"
        alt="{{ config('app.name') }}"
        @class([
            'h-9',
            'dark:hidden' => $darkLogo,
        ])
    />

    @if ($darkLogo)
        <img
            src="{{ $darkLogo->getTemporaryUrl(
                expiration: now()->addMinutes(5),
                conversionName: 'logo-height-250px',
            ) }}"
            alt="{{ config('app.name') }}"
            class="h-9 hidden dark:block"
        />
    @endif
@else
    <img
        src="{{ Vite::asset('resources/images/default-logo-light.png') }}"
        class="h-9 dark:hidden block"

    />

    <img
        src="{{ Vite::asset('resources/images/default-logo-dark.png') }}"
        class="h-9 hidden dark:block"
    />
@endif
