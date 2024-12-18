<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Notifications;

use App\Models\User;
use AdvisingApp\Ai\Models\AiThread;
use App\Models\NotificationSetting;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;

class SendAssistantTranscriptNotification extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function __construct(
        protected AiThread $thread,
        protected User $sender
    ) {}

    public function toEmail(object $notifiable): MailMessage
    {
        $message = MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->greeting("Hello {$notifiable->name},");

        $senderIsNotifiable = $this->sender->is($notifiable);

        if ($senderIsNotifiable) {
            $message->subject("Assistant Chat Transcript: {$this->thread->name}")
                ->line("Here is a copy of your chat with {$this->thread->assistant->name}:");
        } else {
            $message->subject("An Assistant Chat Transcript has been shared with you: {$this->thread->name}")
                ->line("Here is a copy of {$this->sender->name}'s chat with {$this->thread->assistant->name}:");
        }

        $this->thread->messages
            ->each(function (AiMessage $threadMessage) use ($senderIsNotifiable, $message) {
                if ($threadMessage->prompt) {
                    return $message->line(str("**Starting smart prompt:** {$threadMessage->prompt->title}")
                        ->markdown()
                        ->sanitizeHtml()
                        ->toHtmlString());
                }

                if (! $threadMessage->user) {
                    return $message->line(str(nl2br($threadMessage->content))
                        ->prepend("**{$this->thread->assistant->name}:** ")
                        ->markdown()
                        ->sanitizeHtml()
                        ->toHtmlString());
                }

                if ($senderIsNotifiable && $threadMessage->user()->is($this->sender)) {
                    return $message->line(str(nl2br($threadMessage->content))
                        ->prepend('**You:** ')
                        ->markdown()
                        ->sanitizeHtml()
                        ->toHtmlString());
                }

                return $message->line(str(nl2br($threadMessage->content))
                    ->prepend("**{$threadMessage->user->name}:** ")
                    ->markdown()
                    ->sanitizeHtml()
                    ->toHtmlString());
            });

        return $message;
    }

    protected function afterSendHook(object $notifiable, OutboundDeliverable $deliverable): void
    {
        $this->thread->emailed_count = $this->thread->emailed_count + 1;
        $this->thread->save();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->sender->teams()->first()?->division?->notificationSetting?->setting;
    }
}
