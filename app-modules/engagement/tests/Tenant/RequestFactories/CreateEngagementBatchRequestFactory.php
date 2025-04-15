<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Engagement\Tests\Tenant\RequestFactories;

use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Notification\Enums\NotificationChannel;
use App\Models\User;
use Worksome\RequestFactories\RequestFactory;

class CreateEngagementBatchRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'user' => User::factory()->create(),
            // 'subject' => fake()->sentence,
            'subject' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->sentence]]]]],
            'body' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->paragraph]]]]],
            'scheduledAt' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'channel' => fake()->randomElement([NotificationChannel::Email, NotificationChannel::Sms]),
        ];
    }

    public function deliverNow(): self
    {
        return $this->state([
            'scheduledAt' => null,
        ]);
    }

    public function deliverLater(): self
    {
        return $this->state([
            'scheduledAt' => fake()->dateTimeBetween('+1 day', '+1 week'),
        ]);
    }

    public function ofBatch(): self
    {
        return $this->state([
            'engagement_batch_id' => EngagementBatch::factory(),
        ]);
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
}
