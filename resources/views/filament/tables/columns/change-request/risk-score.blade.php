@php
    use AdvisingApp\ServiceManagement\Models\ChangeRequest;
@endphp

<div class="fi-ta-text grid w-full gap-y-1">
    <x-filament::badge class="{{ ChangeRequest::getClassesBasedOnRisk($getState()) }} w-1/2">
        {{ $getState() }}
    </x-filament::badge>
</div>
