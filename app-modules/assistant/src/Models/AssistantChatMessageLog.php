<?php

namespace Assist\Assistant\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Audit\Settings\AuditSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Assistant\Models\AssistantChatMessageLog
 *
 * @property string $id
 * @property string $message
 * @property array $metadata
 * @property string $user_id
 * @property array $request
 * @property int $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|AssistantChatMessageLog newModelQuery()
 * @method static Builder|AssistantChatMessageLog newQuery()
 * @method static Builder|AssistantChatMessageLog query()
 * @method static Builder|AssistantChatMessageLog whereCreatedAt($value)
 * @method static Builder|AssistantChatMessageLog whereId($value)
 * @method static Builder|AssistantChatMessageLog whereMessage($value)
 * @method static Builder|AssistantChatMessageLog whereMetadata($value)
 * @method static Builder|AssistantChatMessageLog whereRequest($value)
 * @method static Builder|AssistantChatMessageLog whereSentAt($value)
 * @method static Builder|AssistantChatMessageLog whereUpdatedAt($value)
 * @method static Builder|AssistantChatMessageLog whereUserId($value)
 *
 * @mixin \Eloquent
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
