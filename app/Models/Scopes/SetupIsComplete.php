<?php

namespace App\Models\Scopes;

namespace App\Models\Scopes;

use Laravel\Pennant\Feature;
use Illuminate\Database\Eloquent\Builder;

class SetupIsComplete
{
    public function __invoke(Builder $query): void
    {
        if (Feature::inactive('setup-complete')) {
            return;
        }

        $query->where('setup_complete', true);
    }
}
