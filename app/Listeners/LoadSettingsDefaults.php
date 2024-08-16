<?php

namespace App\Listeners;

use App\Settings\Contracts\HasDefaultSettings;
use Spatie\LaravelSettings\Events\LoadingSettings;

class LoadSettingsDefaults
{
    public function handle(LoadingSettings $event): void
    {
        if (! is_a($event->settingsClass, HasDefaultSettings::class, allow_string: true)) {
            return;
        }

        foreach ($event->settingsClass::defaults() as $key => $value) {
            if ($event->properties->has($key)) {
                continue;
            }

            $event->properties->put($key, $value);
        }
    }
}
