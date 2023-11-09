<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage as BaseMailMessage;

class MailMessage extends BaseMailMessage
{
    public function emailTemplate(?EmailTemplate $emailTemplate): static
    {
        $this->markdown('vendor.notifications.email', [
            'emailTemplate' => $emailTemplate,
        ]);

        return $this;
    }
}
