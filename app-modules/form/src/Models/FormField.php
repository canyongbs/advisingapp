<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends BaseModel
{
    protected $fillable = [
        'config',
        'label',
        'key',
        'type',
        'required',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this
            ->belongsTo(Form::class);
    }
}
