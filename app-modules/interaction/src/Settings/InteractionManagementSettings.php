<?php

namespace AdvisingApp\Interaction\Settings;

use Spatie\LaravelSettings\Settings;

class InteractionManagementSettings extends Settings
{
    public bool $is_initiative_enabled = true;

    public bool $is_initiative_required = true;

    public bool $is_driver_enabled = true;

    public bool $is_driver_required = true;

    public bool $is_outcome_enabled = true;

    public bool $is_outcome_required = true;

    public bool $is_relation_enabled = true;

    public bool $is_relation_required = true;

    public bool $is_status_enabled = true;

    public bool $is_status_required = true;

    public bool $is_type_enabled = true;

    public bool $is_type_required = true;

    public static function group(): string
    {
        return 'interaction_management';
    }
}
