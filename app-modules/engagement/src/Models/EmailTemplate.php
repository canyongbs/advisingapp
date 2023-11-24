<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;

/**
 * @mixin IdeHelperEmailTemplate
 */
class EmailTemplate extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];
}
