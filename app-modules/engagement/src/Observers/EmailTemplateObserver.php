<?php

namespace Assist\Engagement\Observers;

use Assist\Engagement\Models\EmailTemplate;
use Assist\Engagement\Models\Engagement;

class EmailTemplateObserver
{
    public function creating(EmailTemplate $emailTemplate): void
    {
        if (is_null($emailTemplate->user_id) && ! is_null(auth()->user())) {
            $emailTemplate->user_id = auth()->user()->id;
        }
    }
}
