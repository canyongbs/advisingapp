<?php

namespace AdvisingApp\Form\Notifications;

use App\Models\Tenant;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Form\Models\FormSubmission;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Models\Contracts\NotifiableInterface;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;

class FormSubmissionAutoReplyNotification extends BaseNotification implements EmailNotification, ShouldBeUnique
{
    use EmailChannelTrait;

    public function __construct(
        public FormSubmission $submission
    ) {}

    public function uniqueId(): string
    {
        return Tenant::current()->getKey() . ':' . $this->submission->getKey();
    }

    public function toEmail(NotifiableInterface $notifiable): MailMessage
    {
        /** @var Form $form */
        $form = $this->submission->submissible;

        /** @var Student|Prospect|null $author */
        $author = $this->submission->author;

        return MailMessage::make()
            ->subject($form->emailAutoReply->subject)
            ->content($form->emailAutoReply->getBody($author));
    }
}
