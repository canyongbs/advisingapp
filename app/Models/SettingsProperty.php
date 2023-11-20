<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\LaravelSettings\Models\SettingsProperty as BaseSettingsProperty;

/**
 * @mixin IdeHelperSettingsProperty
 */
class SettingsProperty extends BaseSettingsProperty implements HasMedia
{
    use HasUuids;
    use InteractsWithMedia;

    public static function getInstance(string $property): ?static
    {
        [$group, $name] = explode('.', $property);

        return static::query()
            ->where('group', $group)
            ->where('name', $name)
            ->first();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('logo-height-250px')
            ->performOnCollections('logo', 'dark_logo')
            ->height(250)
            ->keepOriginalImageFormat();
    }
}
