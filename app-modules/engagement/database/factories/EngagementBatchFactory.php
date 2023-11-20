<?php

namespace Assist\Engagement\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Engagement\Models\EngagementBatch>
 */
class EngagementBatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
