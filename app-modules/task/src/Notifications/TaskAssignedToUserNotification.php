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

namespace AdvisingApp\Task\Notifications;

use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ManageProspectTasks;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ViewProspect;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ManageStudentTasks;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use App\Models\NotificationSetting;
use App\Models\User;
use Exception;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskAssignedToUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $truncatedTaskDescription = str($this->task->description)->limit(50);

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject('You have been assigned a new Task')
            ->line('You have been assigned the task: ')
            ->line("\"{$truncatedTaskDescription}\"");
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $url = match (true) {
            $this->task->concern instanceof Student => ManageStudentTasks::getUrl(['record' => $this->task->concern]),
            $this->task->concern instanceof Prospect => ManageProspectTasks::getUrl(['record' => $this->task->concern]),
            default => throw new Exception('Invalid task concern type.')
        };

        $title = str($this->task->title)->limit();

        $message = match (true) {
            $this->task->concern instanceof Student => "You have been assigned a new Task: <a href='{$url}' target='_blank' class='underline'>{$title}</a> related to Student <a href='" . ViewStudent::getUrl(['record' => $this->task->concern]) . "' target='_blank' class='underline'>{$this->task->concern->full_name}</a>",

            $this->task->concern instanceof Prospect => "You have been assigned a new Task: <a href='{$url}' target='_blank' class='underline'>{$title}</a> related to Prospect <a href='" . ViewProspect::getUrl(['record' => $this->task->concern]) . "' target='_blank' class='underline'>{$this->task->concern->full_name}</a>",

            default => throw new Exception('Invalid task concern type.')
        };

        return FilamentNotification::make()
            ->success()
            ->title($message)
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->task->createdBy->team?->division?->notificationSetting?->setting;
    }
}
