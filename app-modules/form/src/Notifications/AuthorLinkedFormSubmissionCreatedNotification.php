<?php

namespace Assist\Form\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Assist\Form\Models\FormSubmission;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;

class AuthorLinkedFormSubmissionCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FormSubmission $submission) {}

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        $author = $this->submission->author;

        $name = $author->{$author->displayNameKey()};

        $target = resolve(Filament::getModelResource($author));

        $formSubmissionUrl = $target::getUrl('manage-form-submissions', ['record' => $author]);

        $formSubmissionLink = new HtmlString("<a href='{$formSubmissionUrl}' target='_blank' class='underline'>form submission</a>");

        $morph = str($author->getMorphClass());

        $morphUrl = $target::getUrl('view', ['record' => $author]);

        $morphLink = new HtmlString("<a href='{$morphUrl}' target='_blank' class='underline'>{$name}</a>");

        return FilamentNotification::make()
            ->warning()
            ->title("A {$formSubmissionLink} has been submitted by {$morph} {$morphLink}")
            ->getDatabaseMessage();
    }
}
