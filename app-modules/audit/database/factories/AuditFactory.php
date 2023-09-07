<?php

namespace Assist\Audit\Database\Factories;

use App\Models\User;
use Assist\Audit\Models\Audit;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audit>
 */
class AuditFactory extends Factory
{
    public function definition(): array
    {
        return [
            'change_agent_type' => (new User())->getMorphClass(),
            'change_agent_id' => User::factory(),
            'event' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'auditable_type' => (new ServiceRequest())->getMorphClass(),
            'auditable_id' => ServiceRequest::factory(),
            'old_values' => ['name' => $this->faker->word()],
            'new_values' => ['name' => $this->faker->word()],
            'url' => $this->faker->url(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
