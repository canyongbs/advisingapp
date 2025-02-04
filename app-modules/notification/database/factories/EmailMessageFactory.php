<?php

namespace AdvisingApp\Notification\Database\Factories;

use AdvisingApp\Notification\Models\EmailMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use TestEmailNotification;

/**
 * @extends Factory<EmailMessage>
 */
class EmailMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'notification_class' => TestEmailNotification::class,
            'quota_usage' => 0,
        ];
    }
}
