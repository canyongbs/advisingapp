<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InAppCommunication\Models;

use AdvisingApp\InAppCommunication\Enums\ConversationType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperTwilioConversation
 */
class TwilioConversation extends Model
{
    use UsesTenantConnection;
    use SoftDeletes;

    protected $primaryKey = 'sid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'is_private_channel' => 'boolean',
        'type' => ConversationType::class,
    ];

    protected $fillable = [
        'sid',
        'friendly_name',
        'type',
        'channel_name',
        'is_private_channel',
    ];

    /**
     * @return BelongsToMany<User, $this>
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'twilio_conversation_user', 'conversation_sid', 'user_id')
            ->withPivot([
                'participant_sid',
                'is_channel_manager',
                'is_pinned',
                'notification_preference',
                'first_unread_message_sid',
                'first_unread_message_at',
                'last_unread_message_content',
                'last_read_at',
                'unread_messages_count',
            ])
            ->withTimestamps()
            ->as('participant')
            ->using(TwilioConversationUser::class);
    }

    public function managers(): BelongsToMany
    {
        return $this->participants()->wherePivot('is_channel_manager', true);
    }

    public function getLabel(): ?string
    {
        if (filled($this->channel_name)) {
            return $this->channel_name;
        }

        return $this->participants->where('id', '!=', auth()->id())->first()?->name;
    }
}
