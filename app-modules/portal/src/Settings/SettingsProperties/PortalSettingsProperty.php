<?php

namespace AdvisingApp\Portal\Settings\SettingsProperties;

use App\Models\SettingsPropertyWithMedia;

class PortalSettingsProperty extends SettingsPropertyWithMedia
{
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/png',
                'image/jpeg',
                'image/webp',
                'image/jpg',
                'image/svg',
            ]);
    }
}
