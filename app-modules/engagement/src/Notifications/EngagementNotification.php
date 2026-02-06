<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Engagement\Notifications;

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Settings\EngagementSettings;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use AdvisingApp\Notification\Models\Contracts\Message;
use AdvisingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AdvisingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Notifications\Messages\TwilioMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Throwable;

class EngagementNotification extends Notification implements ShouldQueue, HasBeforeSendHook, HasAfterSendHook
{
    use Queueable;

    public function __construct(
        public Engagement $engagement
    ) {}

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 30, 60, 300];
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [match ($this->engagement->channel) {
            NotificationChannel::Email => 'mail',
            NotificationChannel::Sms => 'sms',
        }];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->to($this->engagement->recipient_route)
            ->when(
                app(EngagementSettings::class)->are_dynamic_engagements_enabled && $this->engagement->user,
                fn (MailMessage $message) => $message->from(name: $this->engagement->user->name),
            )
            ->subject(strip_tags($this->engagement->getSubject()))
            ->greeting("Hello {$this->engagement->recipient->display_name}!")
            ->content($this->engagement->getBody());
    }

    public function toSms(object $notifiable): TwilioMessage
    {
        return TwilioMessage::make($notifiable)
            ->to($this->engagement->recipient_route)
            ->content(strip_tags($this->engagement->getBodyMarkdown()));
    }

    public function failed(?Throwable $exception): void
    {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        $this->engagement->update([
            'dispatch_failed_at' => now(),
        ]);

        if (is_null($this->engagement->engagement_batch_id)) {
            $this->engagement->user->notify(new EngagementFailedNotification($this->engagement));
        }
    }

    public function beforeSend(AnonymousNotifiable|CanBeNotified $notifiable, Message $message, NotificationChannel $channel): void
    {
        $message->related()->associate($this->engagement);
    }

    public function afterSend(AnonymousNotifiable|CanBeNotified $notifiable, Message $message, NotificationResultData $result): void
    {
        $twilioSettings = app(TwilioSettings::class);

        if (
            $this->engagement->channel === NotificationChannel::Sms
            && $twilioSettings->is_demo_mode_enabled && $twilioSettings->is_demo_auto_reply_mode_enabled
            && $this->engagement->recipient instanceof Model
        ) {
            EngagementResponse::create([
                'type' => EngagementResponseType::Sms,
                'sender_id' => $this->engagement->recipient->getKey(),
                'sender_type' => $this->engagement->recipient->getMorphClass(),
                'content' => 'Thank you for your message. Will get back to you shortly.',
                'sent_at' => now()->addSeconds(2),
                'status' => EngagementResponseStatus::New,
            ]);
        }

        if (! $this->engagement->engagementBatch) {
            return;
        }

        EngagementBatch::query()
            ->whereKey($this->engagement->engagementBatch)
            ->lockForUpdate()
            ->update([
                'processed_engagements' => DB::raw('processed_engagements + 1'),
                ...($result->success ? ['successful_engagements' => DB::raw('successful_engagements + 1')] : []),
            ]);
    }
}
