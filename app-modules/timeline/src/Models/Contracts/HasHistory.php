<?php

namespace AdvisingApp\Timeline\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasHistory
{
    public function histories(): MorphMany;
}
