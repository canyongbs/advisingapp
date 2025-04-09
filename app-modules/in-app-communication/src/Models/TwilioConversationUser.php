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

use AdvisingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AdvisingApp\InAppCommunication\Filament\Pages\UserChat;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperTwilioConversationUser
 */
class TwilioConversationUser extends Pivot
{
    protected $casts = [
        'is_channel_manager' => 'boolean',
        'is_pinned' => 'boolean',
        'first_unread_message_at' => 'immutable_datetime',
        'last_read_at' => 'immutable_datetime',
        'unread_messages_count' => 'integer',
        'notification_preference' => ConversationNotificationPreference::class,
    ];

    public function removeNotification(string $id): void
    {
        $participation = auth()->user()->conversations()->where('sid', $id)->first()?->participant;

        if (! $participation) {
            return;
        }

        $participation->first_unread_message_sid = null;
        $participation->first_unread_message_at = null;
        $participation->last_unread_message_content = null;
        $participation->last_read_at = now();
        $participation->unread_messages_count = 0;
        $participation->save();
    }

    /**
     * @return BelongsTo<TwilioConversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(TwilioConversation::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNotification(): ?Notification
    {
        $notification = null;

        if (! $this->last_read_at) {
            $notification = Notification::make($this->conversation_sid)
                ->title("New chat: {$this->conversation->getLabel()}")
                ->info()
                ->icon('heroicon-o-sparkles');
        } elseif ($this->unread_messages_count) {
            $notification = Notification::make($this->conversation_sid)
                ->title("{$this->unread_messages_count} unread " . str('message')->plural($this->unread_messages_count) . " in {$this->conversation->getLabel()}")
                ->body(Str::limit($this->last_unread_message_content, 50))
                ->warning()
                ->icon('heroicon-o-chat-bubble-left-ellipsis');
        }

        $notification
            ?->actions([
                Action::make('open')
                    ->url(fn (): string => UserChat::getUrl(['conversation' => $this->conversation->sid])),
            ])
            ->date(($this->updated_at ?? $this->created_at)->diffForHumans())
            ->persistent();

        return $notification;
    }
}
