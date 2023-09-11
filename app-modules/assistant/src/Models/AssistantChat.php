<?php

namespace Assist\Assistant\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Assistant\Models\AssistantChat
 *
 * @property string $id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Assistant\Models\AssistantChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereUserId($value)
 *
 * @mixin \Eloquent
 */
class AssistantChat extends BaseModel
{
    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AssistantChatMessage::class);
    }
}
