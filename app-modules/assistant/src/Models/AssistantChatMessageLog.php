<?php

namespace Assist\Assistant\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Audit\Settings\AuditSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAssistantChatMessageLog
 */
class AssistantChatMessageLog extends BaseModel
{
    use MassPrunable;

    protected $fillable = [
        'message',
        'metadata',
        'request',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'encrypted:array',
        'request' => 'encrypted:array',
        'sent_at' => 'timestamp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prunable(): Builder
    {
        return static::where(
            'sent_at',
            '<=',
            now()->subDays(
                app(AuditSettings::class)
                    ->assistant_chat_message_logs_retention_duration_in_days
            ),
        );
    }
}
