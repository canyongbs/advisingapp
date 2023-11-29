<?php

namespace Assist\Engagement\Handlers;

interface DeliverableDriver
{
    public function updateDeliveryStatus(array $data): void;
}
