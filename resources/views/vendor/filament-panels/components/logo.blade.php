@php
    use App\Models\SettingsProperty;
    use Assist\Theme\Settings\ThemeSettings;

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
    <div class="fi-logo text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white">
        {{ config('app.name') }}
    </div>
@endif
