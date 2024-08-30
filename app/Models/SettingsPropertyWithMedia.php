<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

abstract class SettingsPropertyWithMedia extends SettingsProperty implements HasMedia
{
    use InteractsWithMedia;
}
