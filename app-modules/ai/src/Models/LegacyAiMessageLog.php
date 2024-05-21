<?php

namespace AdvisingApp\Ai\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperLegacyAiMessageLog
 */
class LegacyAiMessageLog extends BaseModel
{
    protected $table = 'assistant_chat_message_logs';

    protected $fillable = [
        'message',
        'metadata',
        'request',
        'sent_at',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'encrypted:array',
        'request' => 'encrypted:array',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
