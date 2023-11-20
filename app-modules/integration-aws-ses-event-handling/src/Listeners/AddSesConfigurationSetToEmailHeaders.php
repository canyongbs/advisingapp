<?php

namespace Assist\IntegrationAwsSesEventHandling\Listeners;

use Illuminate\Mail\Events\MessageSending;

class AddSesConfigurationSetToEmailHeaders
{
    public function handle(MessageSending $event): void
    {
        if (filled(config('mail.mailers.ses.configuration_set'))) {
            $event->message->getHeaders()->addTextHeader('X-SES-CONFIGURATION-SET', config('mail.mailers.ses.configuration_set'));
        }
    }
}
