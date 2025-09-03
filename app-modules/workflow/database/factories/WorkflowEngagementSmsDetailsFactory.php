<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Workflow\Models\WorkflowEngagementSmsDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowEngagementSmsDetails>
 */
class WorkflowEngagementSmsDetailsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'channel' => NotificationChannel::Sms,
            'body' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $this->faker->paragraph]]]]],
        ];
    }
}
