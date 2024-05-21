<?php

use function Tests\Helpers\Events\testEventIsBeingListenedTo;

use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesBounceEvent;

testEventIsBeingListenedTo(
    event: SesBounceEvent::class,
    listener: HandleSesBounceEvent::class
);
