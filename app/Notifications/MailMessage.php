<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage as BaseMailMessage;

class MailMessage extends BaseMailMessage
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function emailTemplate(?EmailTemplate $emailTemplate): static
    {
        $this->markdown('vendor.notifications.email', [
            'emailTemplate' => $emailTemplate,
        ]);

        return $this;
    }
}
