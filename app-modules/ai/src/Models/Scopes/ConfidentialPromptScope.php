<?php

namespace AdvisingApp\Ai\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ConfidentialPromptScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('is_confidential', false)->orWhere(function (Builder $query) {
            $query->where('is_confidential', true)
                ->where(function (Builder $query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('confidentialPromptTeams', function (Builder $query) {
                            $query->whereHas('users', function (Builder $query) {
                                $query->where('users.id', auth()->id());
                            });
                        })
                        ->orWhereHas('confidentialPromptUsers', function (Builder $query) {
                            $query->where('users.id', auth()->id());
                        });
                });
        });
    }
}
