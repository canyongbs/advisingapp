@if (!$this->getRecord()->is_external)
    @if ($this->getRecord()->hasConfirmedMultifactor())
        <x-filament::badge
            tooltip="MFA Enabled"
            data-identifier="mfa-enabled"
            color="success"
        >
            {{ __('MFA Enabled') }}
        </x-filament::badge>
    @elseif($this->getRecord()->hasEnabledMultifactor())
        <x-filament::badge
            tooltip="MFA Enabled | Not Confirmed"
            data-identifier="mfa-not-confirmed"
            color="warning"
        >
            {{ __('MFA Enabled | Not Confirmed') }}
        </x-filament::badge>
    @else
        <x-filament::badge
            tooltip="MFA Disabled"
            data-identifier="mfa-disabled"
            color="gray"
        >
            {{ __('MFA Disabled') }}
        </x-filament::badge>
    @endif
@endif
