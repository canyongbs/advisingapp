<?php

namespace App\Settings;

use App\Settings\SettingsProperties\InstitutionDetailsSettingsProperty;
use App\Settings\SettingsWithMedia;
use Spatie\LaravelSettings\Settings;

class InstitutionDetailsSettings extends SettingsWithMedia
{
    public ?string $ipeds_id = null;

    public ?string $name = null;

    public null $dark_logo = null;

    public null $light_logo = null;

    public static function getSettingsPropertyModelClass(): string
    {
        return InstitutionDetailsSettingsProperty::class;
    }

    public static function group(): string
    {
        return 'institution';
    }
}