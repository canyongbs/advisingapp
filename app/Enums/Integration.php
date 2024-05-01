<?php

namespace App\Enums;

use App\Settings\IntegrationSettings;
use Filament\Support\Contracts\HasLabel;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;

enum Integration: string implements HasLabel
{
    case Twilio = 'twilio';

    public function settings(): IntegrationSettings
    {
        return app(match ($this) {
            self::Twilio => TwilioSettings::class,
        });
    }

    public function isOn(): bool
    {
        return $this->settings()->isOn();
    }

    public function isOff(): bool
    {
        return $this->settings()->isOff();
    }

    public function isConfigured(): bool
    {
        return $this->settings()->isConfigured();
    }

    public function isNotConfigured(): bool
    {
        return $this->settings()->isNotConfigured();
    }

    public function isEnabled(): bool
    {
        return $this->settings()->isEnabled();
    }

    public function isDisabled(): bool
    {
        return $this->settings()->isDisabled();
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Twilio => 'Twilio',
        };
    }
}
