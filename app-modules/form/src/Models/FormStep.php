<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Query\Builder;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperFormStep
 */
#[NoPermissions]
class FormStep extends BaseModel
{
    protected $fillable = [
        'label',
        'content',
        'sort',
    ];

    protected $casts = [
        'content' => 'array',
        'sort' => 'integer',
    ];

    public function form(): BelongsTo
    {
        return $this
            ->belongsTo(Form::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class, 'step_id');
    }

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
