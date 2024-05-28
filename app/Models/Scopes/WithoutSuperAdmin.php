<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

class WithoutSuperAdmin
{
    public function __invoke(Builder $query): void 
    {
        $query->whereNot->role('authorization.super_admin');
    }
}
