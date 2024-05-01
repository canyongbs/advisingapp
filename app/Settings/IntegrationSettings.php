<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

abstract class IntegrationSettings extends Settings
{
    public bool $is_enabled;

    abstract public function isConfigured(): bool;

    public function isNotConfigured(): bool
    {
        return ! $this->isConfigured();
    }

    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    public function isDisabled(): bool
    {
        return ! $this->isEnabled();
    }

    public function isOn(): bool
    {
        return $this->is_enabled && $this->isConfigured();
    }

    public function isOff(): bool
    {
        return ! $this->isOn();
    }
}
