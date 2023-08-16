<?php

namespace Assist\Audit\Settings;

use Spatie\LaravelSettings\Settings;

class AuditSettings extends Settings
{
    public int $retention_duration;

    public array $audited_models;

    public static function group(): string
    {
        return 'audit';
    }
}
