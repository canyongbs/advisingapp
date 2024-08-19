<?php

namespace App\Enums;

use Closure;
use Laravel\Pennant\Feature;

enum FeatureFlag: string
{
    case SisIntegrationSettings = 'sis_integration_settings';

    public function definition(): Closure
    {
        return match ($this) {
            default => function () {
                return false;
            }
        };
    }

    public function active(): bool
    {
        return Feature::active($this->value);
    }

    public function activate(): void
    {
        Feature::activate($this->value);
    }

    public function deactivate(): void
    {
        Feature::deactivate($this->value);
    }

    public function purge(): void
    {
        Feature::purge($this->value);
    }
}
