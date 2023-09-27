<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormItem extends BaseModel
{
    protected $fillable = [
        'content',
        'label',
        'key',
        'type',
        'order',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this
            ->belongsTo(Form::class)
            ->orderBy('order');
    }
}
