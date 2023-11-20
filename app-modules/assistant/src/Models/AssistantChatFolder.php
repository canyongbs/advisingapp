<?php

namespace Assist\Assistant\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAssistantChatFolder
 */
class AssistantChatFolder extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function chats(): HasMany
    {
        return $this->hasMany(AssistantChat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
