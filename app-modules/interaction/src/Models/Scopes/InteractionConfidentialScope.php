<?php

namespace AdvisingApp\Interaction\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InteractionConfidentialScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user()?->IsAdmin) {
            return;
        }

        $builder->where('is_confidential', false)->orWhere(function (Builder $builder) {
            $builder->where('is_confidential', true)
                ->where(function ($builder) {
                    $builder->where('user_id', auth()->id())
                        ->orWhereHas('confidentialAccessTeams', function ($builder) {
                            $builder->where('team_id', auth()->user()->current_team_id);
                        })
                        ->orWhereHas('confidentialAccessUsers', function ($builder) {
                            $builder->where('user_id', auth()->id());
                        });
                });
        });
    }
}
