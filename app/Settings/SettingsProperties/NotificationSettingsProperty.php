<?php

namespace App\Settings\SettingsProperties;

use App\Models\SettingsPropertyWithMedia;

class NotificationSettingsProperty extends SettingsPropertyWithMedia
{
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile();
    }
}
