<?php

namespace AdvisingApp\Interaction\Settings;

use Spatie\LaravelSettings\Settings;

class InteractionManagementSettings extends Settings
{
    public bool $is_initiative_enabled;

    public bool $is_initiative_required;

    public bool $is_driver_enabled;

    public bool $is_driver_required;

    public bool $is_outcome_enabled;

    public bool $is_outcome_required;

    public bool $is_relation_enabled;

    public bool $is_relation_required;

    public bool $is_status_enabled;

    public bool $is_status_required;

    public bool $is_type_enabled;

    public bool $is_type_required;

    public static function group(): string
    {
        return 'interaction_management';
    }
}
