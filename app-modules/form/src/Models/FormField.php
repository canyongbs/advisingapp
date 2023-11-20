<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperFormField
 */
class FormField extends BaseModel
{
    protected $fillable = [
        'config',
        'label',
        'key',
        'type',
        'required',
        'form_id',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this
            ->belongsTo(Form::class);
    }

    public function step(): BelongsTo
    {
        return $this
            ->belongsTo(FormStep::class, 'step_id');
    }
}
