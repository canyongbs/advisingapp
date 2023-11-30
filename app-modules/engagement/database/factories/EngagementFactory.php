<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Database\Factories;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<Engagement>
 */
class EngagementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipient_type' => fake()->randomElement([
                (new Student())->getMorphClass(),
                (new Prospect())->getMorphClass(),
            ]),
            'recipient_id' => function (array $attributes) {
                $senderClass = Relation::getMorphedModel($attributes['recipient_type']);

                /** @var Student|Prospect $senderModel */
                $senderModel = new $senderClass();

                $sender = $senderClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $senderModel::factory()->create();

                return $sender->getKey();
            },
            'subject' => fake()->sentence,
            'body' => fake()->paragraph,
            'body_json' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->paragraph]]]]],
            'deliver_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'scheduled' => true,
        ];
    }

    public function forStudent(): self
    {
        return $this->state([
            'recipient_id' => Student::inRandomOrder()->first()->sisid ?? Student::factory(),
            'recipient_type' => (new Student())->getMorphClass(),
        ]);
    }

    public function forProspect(): self
    {
        return $this->state([
            'recipient_id' => Prospect::factory(),
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);
    }

    public function deliverNow(): self
    {
        return $this->state([
            'deliver_at' => now(),
        ]);
    }

    public function deliverLater(): self
    {
        return $this->state([
            'deliver_at' => fake()->dateTimeBetween('+1 day', '+1 week'),
        ]);
    }

    public function ofBatch(): self
    {
        return $this->state([
            'engagement_batch_id' => EngagementBatch::factory(),
        ]);
    }

    public function onDemand(): self
    {
        return $this->state([
            'scheduled' => false,
        ]);
    }
}
