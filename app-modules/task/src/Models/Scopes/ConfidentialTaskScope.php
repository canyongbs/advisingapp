<?php

namespace AdvisingApp\Task\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ConfidentialTaskScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        //feature flag check

        if (auth()->user()?->isSuperAdmin()) {
            return;
        }

        //need to account for projects too!
        $builder->where('is_confidential', false)->orWhere(function (Builder $query) {
            $query->where('is_confidential', true)
                ->where('created_by', auth()->id())
                ->orWhereHas('confidentialAccessTeams', function (Builder $query) {
                    $query->whereHas('users', function (Builder $query) {
                        $query->where('users.id', auth()->id());
                    });
                })
                ->orWhereHas('confidentialAccessUsers', function (Builder $query) {
                    $query->where('user_id', auth()->id());
                });
        });
    }
}
