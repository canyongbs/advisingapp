<?php

namespace Assist\AssistDataModel\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Educatable extends Identifiable
{
    public static function displayNameKey(): string;

    public function careTeam(): MorphToMany;
}
