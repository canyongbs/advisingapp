<?php

use function Tests\Helpers\Events\testEventIsBeingListenedTo;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesDeliveryEvent;

testEventIsBeingListenedTo(
    event: SesDeliveryEvent::class,
    listener: HandleSesDeliveryEvent::class
);
