<?php

namespace Assist\Notifications\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Filament\Notifications\Notification;

class SimulatedNotification extends Command
{
    protected $signature = 'notifications:simulate {--type=info}';

    protected $description = 'This is a temporary command to showcase the notification system.';

    public function handle(): void
    {
        $recipient = User::first();

        Notification::make()
            ->status($this->option('type'))
            ->title('This is a simulated notification')
            ->sendToDatabase($recipient);
    }
}
