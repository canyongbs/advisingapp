<?php

namespace AdvisingApp\Ai\Tests\RequestFactories;

use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use Worksome\RequestFactories\RequestFactory;

class QnaAdvisorQuestionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'answer' => $this->faker->paragraph(),
            'category_id' => QnaAdvisorCategory::factory(),
        ];
    }
}
