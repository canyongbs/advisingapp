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

namespace AdvisingApp\MeetingCenter\Notifications;

use AdvisingApp\MeetingCenter\Filament\Resources\CalendarEventResource;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\NotificationSetting;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CalendarRequiresReconnectNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Calendar $calendar) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->line('The calendar connection for your account needs to be reconnected.')
            ->line('Please reconnect your calendar connection to continue using the calendar for schedules and appointments.')
            ->action('View Schedule and Appointments', CalendarEventResource::getUrl());
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->danger()
            ->title('Your calendar connection needs to be reconnected.')
            ->body('Please reconnect your calendar connection to continue using the calendar for schedules and appointments.')
            ->actions([
                Action::make('reconnect_calendar')
                    ->label('Reconnect Calendar')
                    ->url(CalendarEventResource::getUrl()),
            ])
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $notifiable instanceof User
            ? $notifiable->team?->division?->notificationSetting?->setting
            : null;
    }
}
