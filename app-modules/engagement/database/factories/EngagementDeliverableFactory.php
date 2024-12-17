<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Database\Factories;

use AdvisingApp\Engagement\Models\Engagement;
use Database\Factories\Concerns\RandomizeState;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Engagement\Enums\EngagementDeliveryStatus;

/**
 * @extends Factory<EngagementDeliverable>
 */
class EngagementDeliverableFactory extends Factory
{
    use RandomizeState;

    public function definition(): array
    {
        return [
            'engagement_id' => Engagement::factory(),
            'channel' => fake()->randomElement(NotificationChannel::cases()),
            'delivery_status' => EngagementDeliveryStatus::Awaiting,
            'delivered_at' => null,
            'delivery_response' => null,
        ];
    }

    public function email(): self
    {
        return $this->state([
            'channel' => NotificationChannel::Email,
        ]);
    }

    public function sms(): self
    {
        return $this->state([
            'channel' => NotificationChannel::Sms,
        ]);
    }

    public function deliveryAwaiting(): self
    {
        return $this->state([
            'delivery_status' => EngagementDeliveryStatus::Awaiting,
            'delivered_at' => null,
            'delivery_response' => null,
        ]);
    }

    public function deliverySuccessful(): self
    {
        return $this->state([
            'delivery_status' => EngagementDeliveryStatus::Successful,
            'delivered_at' => now(),
        ]);
    }

    public function deliveryFailed(): self
    {
        return $this->state([
            'delivery_status' => EngagementDeliveryStatus::Failed,
            'delivered_at' => null,
            'delivery_response' => 'Something went wrong when trying to deliver the engagement.',
        ]);
    }
}
