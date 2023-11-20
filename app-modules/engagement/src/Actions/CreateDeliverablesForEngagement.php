<?php

namespace Assist\Engagement\Actions;

use Assist\Engagement\Models\Engagement;

class CreateDeliverablesForEngagement
{
    public function __invoke(Engagement $engagement, array|string $deliveryMethods): void
    {
        $deliveryMethods = is_array($deliveryMethods) ? $deliveryMethods : [$deliveryMethods];

        foreach ($deliveryMethods as $deliveryMethod) {
            $engagement->deliverables()->create([
                'channel' => $deliveryMethod,
            ]);
        }
    }
}
