<?php

namespace AdvisingApp\Assistant\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptUpvote extends BaseModel
{
    protected $fillable = [
        'user_id',
    ];

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
