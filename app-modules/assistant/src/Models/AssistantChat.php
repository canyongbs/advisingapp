<?php

namespace Assist\Assistant\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAssistantChat
 */
class AssistantChat extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AssistantChatMessage::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(AssistantChatFolder::class, 'assistant_chat_folder_id');
    }
}
