<?php

namespace AdvisingApp\Notification\Database\Factories;

use AdvisingApp\Notification\Models\DatabaseMessage;
use AdvisingApp\Notification\Tests\Fixtures\TestDatabaseNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DatabaseMessage>
 */
class DatabaseMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'notification_class' => TestDatabaseNotification::class,
            'quota_usage' => 0,
        ];
    }
}
