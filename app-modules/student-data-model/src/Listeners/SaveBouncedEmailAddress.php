<?php

namespace AdvisingApp\StudentDataModel\Listeners;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesEvent;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use App\Features\BouncedEmailAddressFeature;

class SaveBouncedEmailAddress extends HandleSesEvent
{
    public function handle(SesEvent $event): void
    {
        if (! BouncedEmailAddressFeature::active()) {
            return;
        }

        if ($event->data->bounce->bounceType !== 'Permanent') {
            return;
        }

        foreach ($event->data->bounce->bouncedRecipients as $bouncedRecipient) {
            BouncedEmailAddress::firstOrCreate([
                'address' => $bouncedRecipient->emailAddress,
            ]);
        }
    }
}
