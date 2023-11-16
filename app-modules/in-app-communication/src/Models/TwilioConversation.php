<?php

namespace Assist\InAppCommunication\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Assist\InAppCommunication\Enums\ConversationType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTwilioConversation
 */
class TwilioConversation extends Model
{
    protected $primaryKey = 'sid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'type' => ConversationType::class,
    ];

    protected $fillable = [
        'sid',
        'friendly_name',
        'type',
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'twilio_conversation_user', 'conversation_sid', 'user_id')
            ->withPivot('participant_sid')
            ->withTimestamps()
            ->as('participant');
    }
}
