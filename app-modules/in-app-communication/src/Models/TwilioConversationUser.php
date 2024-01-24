<?php

namespace AdvisingApp\InAppCommunication\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperTwilioConversationUser
 */
class TwilioConversationUser extends Pivot
{
    protected $casts = [
        'is_channel_manager' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(TwilioConversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
