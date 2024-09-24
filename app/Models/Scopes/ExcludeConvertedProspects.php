<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

class ExcludeConvertedProspects
{
    public function __invoke(Builder $query): void
    {
        $query->doesntHave('student');
    }
}
