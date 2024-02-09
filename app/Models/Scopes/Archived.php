<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

class Archived
{
    public function __invoke(Builder $query): void
    {
        $query->whereNotNull('archived_at');
    }
}
