<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAssistantUpvote extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'assistant_id',
        'user_id',
    ];

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
