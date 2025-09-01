@php
    use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
    $settings = app(InteractionManagementSettings::class);
@endphp

<x-filament-panels::page>
    <div class="mb-6">
        {{ $this->form }}
    </div>

    @if ($settings->is_driver_enabled)
        {{ $this->table }}
    @else
        <div class="py-8 text-center text-gray-500">
            Drivers are currently hidden from all interactions (create, edit, view, and list).
            Enable the feature above to make drivers available again.
        </div>
    @endif
</x-filament-panels::page>
