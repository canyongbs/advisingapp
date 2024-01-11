<?php

namespace AdvisingApp\MeetingCenter\Notifications;

use App\Models\User;
use App\Models\NotificationSetting;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Notifications\Actions\Action;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\DatabaseNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AdvisingApp\MeetingCenter\Filament\Resources\CalendarEventResource;
use AdvisingApp\Notification\Notifications\Concerns\DatabaseChannelTrait;

class CalendarRequiresReconnect extends BaseNotification implements EmailNotification, DatabaseNotification
{
    use EmailChannelTrait;
    use DatabaseChannelTrait;

    public function __construct(public Calendar $calendar) {}

    public function toEmail(User|Student|Prospect|EventAttendee $notifiable): MailMessage
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

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $notifiable->teams()->first()?->division?->notificationSetting?->setting;
    }
}
