<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\LaravelSettings\Models\SettingsProperty as BaseSettingsProperty;

/**
 * App\Models\SettingsProperty
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property mixed $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereUpdatedAt($value)
 *
 * @mixin \Eloquent
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
            ->performOnCollections('logo')
            ->height(250);
    }
}
