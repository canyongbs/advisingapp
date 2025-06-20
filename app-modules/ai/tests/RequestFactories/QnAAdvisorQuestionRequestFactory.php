<?php

namespace AdvisingApp\Ai\Tests\RequestFactories;

use AdvisingApp\Ai\Models\QnAAdvisorCategory;
use Worksome\RequestFactories\RequestFactory;

class QnAAdvisorQuestionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'answer' => $this->faker->paragraph(),
            'category_id' => QnAAdvisorCategory::factory(),
        ];
    }
}
