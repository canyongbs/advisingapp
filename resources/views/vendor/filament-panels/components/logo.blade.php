@php
    use App\Models\SettingsProperty;
    use Assist\Theme\Settings\ThemeSettings;

    $themeSettings = app(ThemeSettings::class);
@endphp

@if ($themeSettings->is_logo_active)
    <img
        src="{{
            SettingsProperty::getInstance('theme.is_logo_active')
                ->getFirstMedia('logo')
                ->getTemporaryUrl(
                    expiration: now()->addMinutes(5),
                    conversionName: 'logo-height-250px',
                )
        }}"
        alt="{{ config('app.name') }}"
        class="h-9"
    />
@else
    <div class="fi-logo text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white">
        {{ config('app.name') }}
    </div>
@endif
