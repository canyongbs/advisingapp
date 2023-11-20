<?php

namespace Assist\AssistDataModel\Models\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
* @property-read Collection $careTeam
 */
interface Educatable extends Identifiable
{
    public static function displayNameKey(): string;

    public function careTeam(): MorphToMany;
}
