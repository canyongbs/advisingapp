<?php

namespace Assist\Assistant\Models;

use App\Models\BaseModel;

class AssistantChatMessage extends BaseModel
{
    protected $fillable = [
        'assistant_chat_id',
        'message',
        'from',
    ];

    public function chat()
    {
        return $this->belongsTo(AssistantChat::class);
    }
}
