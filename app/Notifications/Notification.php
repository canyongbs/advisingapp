<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Notifications\Notification as BaseNotification;

abstract class Notification extends BaseNotification
{
    public EmailTemplate $emailTemplate;

    public function __construct()
    {
        $this->emailTemplate = $this->resolveEmailTemplate();
    }

    abstract protected function resolveEmailTemplate(): ?EmailTemplate;
}
