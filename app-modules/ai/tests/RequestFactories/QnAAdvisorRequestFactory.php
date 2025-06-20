<?php

namespace AdvisingApp\Ai\Tests\RequestFactories;

use AdvisingApp\Ai\Enums\AiModel;
use Illuminate\Http\UploadedFile;
use Worksome\RequestFactories\RequestFactory;

class QnAAdvisorRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'avatar' => UploadedFile::fake()->image(fake()->word . '.png'),
            'name' => fake()->word(),
            'description' => fake()->paragraph(),
            'model' => AiModel::Test,
        ];
    }
}