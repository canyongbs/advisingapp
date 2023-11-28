<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Query\Builder;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $label
 * @property array $content
 * @property-read Submissible $submissible
 * @property-read Collection<int, SubmissibleField> $fields
 */
#[NoPermissions]
abstract class SubmissibleStep extends BaseModel
{
    abstract public function submissible(): BelongsTo;

    abstract public function fields(): HasMany;

    abstract protected function label(): Attribute;

    abstract protected function content(): Attribute;

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
