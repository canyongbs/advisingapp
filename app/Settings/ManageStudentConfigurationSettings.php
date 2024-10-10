<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ManageStudentConfigurationSettings extends Settings
{
    public bool $is_enabled = false;

    public static function group(): string
    {
        return 'manageable-student';
    }
}
