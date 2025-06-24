<?php

namespace AdvisingApp\Ai\Tests\RequestFactories;

use AdvisingApp\Ai\Enums\AiModel;
use Illuminate\Http\UploadedFile;
use Worksome\RequestFactories\RequestFactory;

class QnaAdvisorRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'avatar' => UploadedFile::fake()->image(fake()->word . '.png'),
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'model' => AiModel::Test,
        ];
    }
}
