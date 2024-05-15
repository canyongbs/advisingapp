<?php

namespace AdvisingApp\Ai\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class AiMessage extends BaseModel
{
    use AsPivot;
    use SoftDeletes;

    protected $fillable = [
        'message_id',
        'content',
        'thread_id',
        'user_id',
    ];

    protected $table = 'ai_messages';

    public function thread(): BelongsTo
    {
        return $this->belongsTo(AiThread::class, 'thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
