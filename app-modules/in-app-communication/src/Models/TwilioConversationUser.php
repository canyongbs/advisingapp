<?php

namespace AdvisingApp\InAppCommunication\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TwilioConversationUser extends Pivot
{
    protected $casts = [
        'is_chanel_manager' => 'boolean',
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
