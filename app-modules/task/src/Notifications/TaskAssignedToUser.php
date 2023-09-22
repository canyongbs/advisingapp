<?php

namespace Assist\Task\Notifications;

use App\Models\User;
use Assist\Task\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

class TaskAssignedToUser extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Task $task,
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $truncatedTaskDescription = str($this->task->description)->limit(50);

        return (new MailMessage())
            ->subject('You have been assigned a new Task')
            ->line('You have been assigned the task: ')
            ->line("\"{$truncatedTaskDescription}\"");
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->status('success')
            ->title('You have been assigned a new Task: ' . str($this->task->description)->limit(50))
            ->toDatabase()
            ->data;
    }
}
