<?php

namespace Assist\Alert\Models\Concerns;

use Assist\Alert\Models\Alert;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAlerts
{
    public function alerts(): MorphMany
    {
        return $this->morphMany(Alert::class, 'concern');
    }
}
