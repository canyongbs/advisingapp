@php
    use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
    $settings = app(InteractionManagementSettings::class);
@endphp

<x-filament-panels::page>
    <div class="mb-6">
        {{ $this->form }}
    </div>

    @if ($settings->is_type_enabled)
        {{ $this->table }}
    @else
        <div class="py-8 text-center text-gray-500">
            Types are currently hidden from all interactions (create, edit, view, and list).
            Enable the feature above to make types available again.
        </div>
    @endif
</x-filament-panels::page>
