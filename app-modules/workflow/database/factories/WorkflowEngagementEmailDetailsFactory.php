<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowEngagementEmailDetails>
 */
class WorkflowEngagementEmailDetailsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'channel' => NotificationChannel::Email,
            'subject' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $this->faker->sentence]]]]],
            'body' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $this->faker->paragraphs(3, true)]]]]],
        ];
    }
}
