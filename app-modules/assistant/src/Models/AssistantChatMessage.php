<?php

namespace Assist\Assistant\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

/**
 * @mixin IdeHelperAssistantChatMessage
 */
class AssistantChatMessage extends BaseModel
{
    protected $fillable = [
        'message',
        'from',
    ];

    protected $casts = [
        'from' => AIChatMessageFrom::class,
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(AssistantChat::class, 'assistant_chat_id');
    }
}
