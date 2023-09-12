<?php

namespace Assist\Assistant\Models;

use Eloquent;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Assistant\Models\AssistantChatMessage
 *
 * @property string $id
 * @property string $assistant_chat_id
 * @property string $message
 * @property string $from
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Assist\Assistant\Models\AssistantChat|null $chat
 *
 * @method static Builder|AssistantChatMessage newModelQuery()
 * @method static Builder|AssistantChatMessage newQuery()
 * @method static Builder|AssistantChatMessage query()
 * @method static Builder|AssistantChatMessage whereAssistantChatId($value)
 * @method static Builder|AssistantChatMessage whereCreatedAt($value)
 * @method static Builder|AssistantChatMessage whereFrom($value)
 * @method static Builder|AssistantChatMessage whereId($value)
 * @method static Builder|AssistantChatMessage whereMessage($value)
 * @method static Builder|AssistantChatMessage whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class AssistantChatMessage extends BaseModel
{
    protected $fillable = [
        'assistant_chat_id',
        'message',
        'from',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(AssistantChat::class);
    }
}
