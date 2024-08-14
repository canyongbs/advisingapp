<x-filament::badge
    :tooltip="$this->getRecord()->hasConfirmedMultifactor()
        ? 'MFA Enabled'
        : ($this->getRecord()->hasEnabledMultifactor()
            ? 'MFA Enabled | Not Confirmed'
            : 'MFA Disabled')"
    :color="$this->getRecord()->hasConfirmedMultifactor()
        ? 'success'
        : ($this->getRecord()->hasEnabledMultifactor()
            ? 'warning'
            : 'gray')"
>
    {{ $this->getRecord()->hasConfirmedMultifactor() ? 'MFA Enabled' : ($this->getRecord()->hasEnabledMultifactor() ? 'MFA Enabled | Not Confirmed' : 'MFA Disabled') }}
</x-filament::badge>
