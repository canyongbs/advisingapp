@props(['url', 'emailTemplate' => null])
@php
    use App\Models\SettingsProperty;
    use Assist\Theme\Settings\ThemeSettings;

    $themeSettings = app(ThemeSettings::class);
    $settingsProperty = SettingsProperty::getInstance('theme.is_logo_active');
    $logo = $settingsProperty->getFirstMedia('logo');
@endphp
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if ($emailTemplate?->hasMedia('logo'))
                {{-- TODO: Don't use temporary urls? --}}
                <img src="{{ $emailTemplate?->getFirstTemporaryUrl(now()->addDays(6), 'logo') }}"
                     style="height: 75px; max-height: 75px; max-width: 100vw;"
                     alt="{{ config('app.name') }}">
            @elseif ($themeSettings->is_logo_active && $logo)
                <img src="{{ $logo->getTemporaryUrl(now()->addDays(6)) }}"
                     style="height: 75px; max-height: 75px; max-width: 100vw;"
                     alt="{{ config('app.name') }}">
            @else
                <img src="{{ Vite::asset('resources/images/default-logo-light.png') }}"
                     style="height: 75px; max-height: 75px; max-width: 100vw;"
                     alt="{{ config('app.name') }}">
            @endif
        </a>
    </td>
</tr>
