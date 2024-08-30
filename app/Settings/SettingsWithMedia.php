<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
use App\Models\SettingsPropertyWithMedia;
use Spatie\LaravelSettings\SettingsRepositories\SettingsRepository;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;

abstract class SettingsWithMedia extends Settings
{
    /**
     * @return class-string<SettingsPropertyWithMedia>
     */
    abstract public static function getSettingsPropertyModelClass(): string;

    public function getRepository(): SettingsRepository
    {
        return new DatabaseSettingsRepository([
            ...config('settings.repositories.database'),
            ...[
                'model' => static::getSettingsPropertyModelClass(),
            ],
        ]);
    }

    public static function getSettingsPropertyModel(string $property): SettingsPropertyWithMedia
    {
        return static::getSettingsPropertyModelClass()::getInstance($property);
    }
}
