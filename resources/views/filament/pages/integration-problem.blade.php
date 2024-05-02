@props(['integration'])
<x-filament-panels::page>
    @php/** @var \App\Enums\Integration $integration */@endphp
    @if ($integration->isDisabled())
        A required setting is disabled. Please contact your administrator to enable it.
    @elseif($integration->isNotConfigured())
        A required setting is not configured. Please contact your administrator to configure it.
    @else
        Something has gone wrong. Please contact your administrator.
    @endif
</x-filament-panels::page>
