<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait Tenantable
{
    protected static function bootTenantable(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        static::creating(function (Model $model) {
            if (! auth()->check()) {
                return;
            }

            $model->owner_id = auth()->id();
        });

        static::addGlobalScope('owner_filter', function (Builder $builder) {
            if (optional(auth()->user())->is_admin) {
                return;
            }

            $builder->where((new static())->getTable() . '.owner_id', auth()->id());
        });
    }
}
