<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Query\Builder;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Submissible $submissible
 */
#[NoPermissions]
abstract class SubmissibleStep extends BaseModel
{
    abstract public function submissible(): BelongsTo;

    abstract public function fields(): HasMany;

    protected static function boot(): void
    {
        parent::boot();

        static::saving(
            fn (FormStep $step) => $step->sort ??= $step->form->steps->count(),
        );

        static::withGlobalScope(
            'sort',
            fn (Builder $query) => $query->orderBy('sort'),
        );
    }
}
