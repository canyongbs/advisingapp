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
