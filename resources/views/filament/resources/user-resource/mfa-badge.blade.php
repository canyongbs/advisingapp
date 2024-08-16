@if (!$this->getRecord()->is_external)
    @if ($this->getRecord()->hasConfirmedMultifactor())
        <x-filament::badge
            tooltip="MFA Enabled"
            color="success"
        >
            {{ __('MFA Enabled') }}
        </x-filament::badge>
    @elseif($this->getRecord()->hasEnabledMultifactor())
        <x-filament::badge
            tooltip="MFA Enabled | Not Confirmed"
            color="warning"
        >
            {{ __('MFA Enabled | Not Confirmed') }}
        </x-filament::badge>
    @else
        <x-filament::badge
            tooltip="MFA Disabled"
            color="gray"
        >
            {{ __('MFA Disabled') }}
        </x-filament::badge>
    @endif
@endif
