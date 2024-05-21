<?php

use function Tests\Helpers\Events\testEventIsBeingListenedTo;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryDelayEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesDeliveryDelayEvent;

testEventIsBeingListenedTo(
    event: SesDeliveryDelayEvent::class,
    listener: HandleSesDeliveryDelayEvent::class
);
