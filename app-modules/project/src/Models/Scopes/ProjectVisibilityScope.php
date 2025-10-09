<?php

namespace AdvisingApp\Project\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProjectVisibilityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        $builder->where(function (Builder $query) use ($user) {
            $query->whereMorphedTo('createdBy', $user)
                ->orWhereHas('managerTeams.users', fn (Builder $query) => $query->where('id', $user->getKey()))
                ->orWhereHas('auditorTeams.users', fn (Builder $query) => $query->where('id', $user->getKey()))
                ->orWhereHas('managerUsers', fn (Builder $query) => $query->where('user_id', $user->getKey()))
                ->orWhereHas('auditorUsers', fn (Builder $query) => $query->where('user_id', $user->getKey()));
        });
    }
}
