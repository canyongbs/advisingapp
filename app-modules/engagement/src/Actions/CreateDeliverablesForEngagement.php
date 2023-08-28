<?php

namespace Assist\Engagement\Actions;

use Assist\Engagement\Models\Engagement;

class CreateDeliverablesForEngagement
{
    public function __invoke(Engagement $engagement, array $deliveryMethods): void
    {
        foreach ($deliveryMethods as $deliveryMethod) {
            $engagement->deliverables()->create([
                'channel' => $deliveryMethod,
            ]);
        }
    }
}
