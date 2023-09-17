<?php

namespace Assist\Assistant\Models;

use Eloquent;
use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Assistant\Models\AssistantChat
 *
 * @property string $id
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \Assist\Assistant\Models\AssistantChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read User $user
 *
 * @method static Builder|AssistantChat newModelQuery()
 * @method static Builder|AssistantChat newQuery()
 * @method static Builder|AssistantChat query()
 * @method static Builder|AssistantChat whereCreatedAt($value)
 * @method static Builder|AssistantChat whereId($value)
 * @method static Builder|AssistantChat whereUpdatedAt($value)
 * @method static Builder|AssistantChat whereUserId($value)
 *
 * @mixin Eloquent
 * @mixin IdeHelperAssistantChat
 */
class AssistantChat extends BaseModel
{
    protected $fillable = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AssistantChatMessage::class);
    }
}
