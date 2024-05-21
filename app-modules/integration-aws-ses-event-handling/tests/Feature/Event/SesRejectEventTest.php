<?php

use function Tests\Helpers\Events\testEventIsBeingListenedTo;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesRejectEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesRejectEvent;

testEventIsBeingListenedTo(
    event: SesRejectEvent::class,
    listener: HandleSesRejectEvent::class
);
