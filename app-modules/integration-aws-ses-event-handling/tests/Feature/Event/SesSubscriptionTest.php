<?php

use function Tests\Helpers\Events\testEventIsBeingListenedTo;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesSubscriptionEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesSubscriptionEvent;

testEventIsBeingListenedTo(
    event: SesSubscriptionEvent::class,
    listener: HandleSesSubscriptionEvent::class
);
