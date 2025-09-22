<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperAiAssistantUse
 */
class AiAssistantUse extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
    ];

    /**
     * @return BelongsTo<AiAssistant, $this>
     */
    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
