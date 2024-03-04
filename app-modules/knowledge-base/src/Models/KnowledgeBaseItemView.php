<?php

namespace AdvisingApp\KnowledgeBase\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseItemView extends BaseModel
{
    protected $fillable = [
        'user_id',
    ];

    public function knowledgeBaseItem(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
