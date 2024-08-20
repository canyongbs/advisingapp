@use ('Filament\Support\Colors\Color')
@use ('App\Settings\CollegeBrandingSettings')
@php
    $color = Color::all()[app(CollegeBrandingSettings::class)->color ?? 'blue'];
@endphp
@if (app(CollegeBrandingSettings::class)->is_enabled)
    <div
        class="p-2 text-white"
        style="background-color:rgb({{ $color['800'] }})"
    >
        <div class="container mx-auto">
            <span>{{ app(CollegeBrandingSettings::class)->college_text }}</span>
        </div>
    </div>
@endif
