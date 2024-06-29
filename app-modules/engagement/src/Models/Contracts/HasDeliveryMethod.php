<?php

namespace AdvisingApp\Engagement\Models\Contracts;

use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;

interface HasDeliveryMethod
{
    public function getDeliveryMethod(): EngagementDeliveryMethod;
}
