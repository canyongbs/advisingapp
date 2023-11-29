<?php

namespace Assist\Engagement\Drivers;

interface DeliverableDriver
{
    public function updateDeliveryStatus(array $data): void;
}
