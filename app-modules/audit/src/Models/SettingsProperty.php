<?php

namespace Assist\Audit\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\LaravelSettings\Models\SettingsProperty as BaseSettingsProperty;

class SettingsProperty extends BaseSettingsProperty
{
    use HasUuids;
}
