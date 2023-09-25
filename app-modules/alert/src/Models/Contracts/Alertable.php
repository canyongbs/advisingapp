<?php

namespace Assist\Alert\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Alertable
{
    public function alerts(): MorphMany;
}
