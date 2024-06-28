<?php

namespace AdvisingApp\Ai\Listeners;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use AdvisingApp\Ai\Events\AssistantFilesFinishedUploading;

class HandleAssistantFilesFinishedUploading implements ShouldQueue
{
    public function __construct() {}

    public function handle(AssistantFilesFinishedUploading $event): void
    {
        $filesLanguage = $event->files->count() > 1 ? 'files were' : 'file was';

        Notification::make()
            ->title($event->files->count() . ' ' . $filesLanguage . ' successfully uploaded to the ' . $event->assistant->name . ' assistant')
            ->success()
            ->sendToDatabase($event->user);
    }
}
