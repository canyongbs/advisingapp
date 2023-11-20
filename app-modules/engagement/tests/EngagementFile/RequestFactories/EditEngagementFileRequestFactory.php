<?php

namespace Assist\Engagement\Tests\EngagementFile\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class EditEngagementFileRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(3),
            'file' => 'fake-file.pdf',
        ];
    }
}
