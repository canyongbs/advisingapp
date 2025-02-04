<?php

namespace AdvisingApp\Notification\Database\Factories;

use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Notification\Tests\Fixtures\TestSmsNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SmsMessage>
 */
class SmsMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'notification_class' => TestSmsNotification::class,
            'quota_usage' => 0,
        ];
    }
}
