<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;

/**
 * @mixin IdeHelperSmsTemplate
 */
class SmsTemplate extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'content',
    ];
}
