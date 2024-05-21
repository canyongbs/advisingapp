<?php

use function Tests\Helpers\Events\testEventIsBeingListenedTo;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesRenderingFailureEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesRenderingFailureEvent;

testEventIsBeingListenedTo(
    event: SesRenderingFailureEvent::class,
    listener: HandleSesRenderingFailureEvent::class
);
