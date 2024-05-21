<?php

use function Tests\Helpers\Events\testEventIsBeingListenedTo;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesComplaintEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesComplaintEvent;

testEventIsBeingListenedTo(
    event: SesComplaintEvent::class,
    listener: HandleSesComplaintEvent::class
);
