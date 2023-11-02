<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
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
}
