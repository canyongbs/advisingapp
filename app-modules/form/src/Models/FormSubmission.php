<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperFormSubmission
 */
class FormSubmission extends BaseModel
{
    protected $fillable = [
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
