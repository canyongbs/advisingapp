<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Actions\GetQnaAdvisorInstructions;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use AdvisingApp\Ai\Models\QnaAdvisorQuestion;
use AdvisingApp\Ai\Settings\AiQnaAdvisorSettings;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    // Clear the cache before each test
    Cache::tags(['{qna_advisor_instructions}'])->flush();
});

it('returns the correct instructions for a QnaAdvisor', function () {
    // Setup settings
    $settings = app(AiQnaAdvisorSettings::class);
    $settings->instructions = 'Test instructions content';
    $settings->background_information = 'Test background information';
    $settings->restrictions = 'Test restrictions content';
    $settings->save();

    // Create QnaAdvisor with two categories, each with two questions
    $qnaAdvisor = QnaAdvisor::factory()->create();

    $categoryOne = QnaAdvisorCategory::factory()->create([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Academic Policies',
        'description' => 'Information about academic policies and procedures',
    ]);

    $categoryTwo = QnaAdvisorCategory::factory()->create([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Financial Aid',
        'description' => 'Questions related to financial aid and scholarships',
    ]);

    // Create two questions for each category
    $questionOne = QnaAdvisorQuestion::factory()->create([
        'category_id' => $categoryOne->getKey(),
        'question' => 'What is the minimum GPA requirement?',
        'answer' => 'The minimum GPA requirement is 2.0 for undergraduate students.',
    ]);

    $questionTwo = QnaAdvisorQuestion::factory()->create([
        'category_id' => $categoryOne->getKey(),
        'question' => 'How do I appeal a grade?',
        'answer' => 'You can appeal a grade by submitting a formal appeal form to the registrar.',
    ]);

    $questionThree = QnaAdvisorQuestion::factory()->create([
        'category_id' => $categoryTwo->getKey(),
        'question' => 'When is the FAFSA deadline?',
        'answer' => 'The FAFSA deadline is typically March 1st for the following academic year.',
    ]);

    $questionFour = QnaAdvisorQuestion::factory()->create([
        'category_id' => $categoryTwo->getKey(),
        'question' => 'What scholarships are available?',
        'answer' => 'There are merit-based, need-based, and departmental scholarships available.',
    ]);

    // Execute the action
    $action = new GetQnaAdvisorInstructions();
    $result = $action->execute($qnaAdvisor);

    // Verify the structure and content
    expect($result)->toContain('# Instructions');
    expect($result)->toContain('Test instructions content');

    expect($result)->toContain('## Institutional Background Information');
    expect($result)->toContain('Test background information');

    expect($result)->toContain('## Questions and Answers');
    expect($result)->toContain('This section contains the specialized knowledge');

    expect($result)->toContain('## Restrictions');
    expect($result)->toContain('Test restrictions content');

    // Verify categories are included
    expect($result)->toContain('### Category');
    expect($result)->toContain('Academic Policies');
    expect($result)->toContain('Information about academic policies and procedures');
    expect($result)->toContain('Financial Aid');
    expect($result)->toContain('Questions related to financial aid and scholarships');

    // Verify questions are included with proper formatting
    expect($result)->toContain('#### What is the minimum GPA requirement?');
    expect($result)->toContain('The minimum GPA requirement is 2.0 for undergraduate students.');
    expect($result)->toContain('#### How do I appeal a grade?');
    expect($result)->toContain('You can appeal a grade by submitting a formal appeal form to the registrar.');
    expect($result)->toContain('#### When is the FAFSA deadline?');
    expect($result)->toContain('The FAFSA deadline is typically March 1st for the following academic year.');
    expect($result)->toContain('#### What scholarships are available?');
    expect($result)->toContain('There are merit-based, need-based, and departmental scholarships available.');

    // Verify the markdown structure is correct
    expect($result)->toMatch('/# Instructions\s+Test instructions content/');
    expect($result)->toMatch('/## Institutional Background Information\s+Test background information/');
    expect($result)->toMatch('/## Questions and Answers\s+This section contains/');
    expect($result)->toMatch('/## Restrictions\s+Test restrictions content/');
});

it('does not contain details on a category that has no questions', function () {
    // Setup settings
    $settings = app(AiQnaAdvisorSettings::class);
    $settings->instructions = 'Test instructions';
    $settings->background_information = 'Test background';
    $settings->restrictions = 'Test restrictions';
    $settings->save();

    // Create QnaAdvisor with one category that has no questions
    $qnaAdvisor = QnaAdvisor::factory()->create();

    $categoryWithQuestions = QnaAdvisorCategory::factory()->create([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Academic Policies',
        'description' => 'Information about academic policies',
    ]);

    $categoryWithoutQuestions = QnaAdvisorCategory::factory()->create([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Empty Category',
        'description' => 'This category has no questions',
    ]);

    // Create questions only for the first category
    QnaAdvisorQuestion::factory()->create([
        'category_id' => $categoryWithQuestions->getKey(),
        'question' => 'What is the GPA requirement?',
        'answer' => 'The GPA requirement is 2.0.',
    ]);

    // Execute the action
    $action = new GetQnaAdvisorInstructions();
    $result = $action->execute($qnaAdvisor);

    // Verify the category with questions is included
    expect($result)->toContain('Academic Policies');
    expect($result)->toContain('Information about academic policies');
    expect($result)->toContain('#### What is the GPA requirement?');
    expect($result)->toContain('The GPA requirement is 2.0.');

    // Verify the category without questions is NOT included
    expect($result)->not->toContain('Empty Category');
    expect($result)->not->toContain('This category has no questions');

    // Verify the basic structure is still intact
    expect($result)->toContain('# Instructions');
    expect($result)->toContain('## Questions and Answers');
    expect($result)->toContain('## Restrictions');
});

it('handles empty settings gracefully', function () {
    // Setup empty settings
    $settings = app(AiQnaAdvisorSettings::class);
    $settings->instructions = null;
    $settings->background_information = null;
    $settings->restrictions = null;
    $settings->save();

    // Create QnaAdvisor with some data
    $qnaAdvisor = QnaAdvisor::factory()->create();

    $category = QnaAdvisorCategory::factory()->create([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Test Category',
        'description' => 'Test description',
    ]);

    QnaAdvisorQuestion::factory()->create([
        'category_id' => $category->getKey(),
        'question' => 'Test question?',
        'answer' => 'Test answer.',
    ]);

    // Execute the action
    $action = new GetQnaAdvisorInstructions();
    $result = $action->execute($qnaAdvisor);

    // Verify the structure is maintained even with empty settings
    expect($result)->toContain('# Instructions');
    expect($result)->toContain('## Institutional Background Information');
    expect($result)->toContain('## Questions and Answers');
    expect($result)->toContain('## Restrictions');

    // Verify the QnA section is still populated
    expect($result)->toContain('Test Category');
    expect($result)->toContain('#### Test question?');
    expect($result)->toContain('Test answer.');
});

it('handles QnaAdvisor with no categories', function () {
    // Setup settings
    $settings = app(AiQnaAdvisorSettings::class);
    $settings->instructions = 'Test instructions';
    $settings->background_information = 'Test background';
    $settings->restrictions = 'Test restrictions';
    $settings->save();

    // Create QnaAdvisor with no categories
    $qnaAdvisor = QnaAdvisor::factory()->create();

    // Execute the action
    $action = new GetQnaAdvisorInstructions();
    $result = $action->execute($qnaAdvisor);

    // Verify the basic structure is maintained
    expect($result)->toContain('# Instructions');
    expect($result)->toContain('Test instructions');
    expect($result)->toContain('## Institutional Background Information');
    expect($result)->toContain('Test background');
    expect($result)->toContain('## Questions and Answers');
    expect($result)->toContain('This section contains the specialized knowledge');
    expect($result)->toContain('## Restrictions');
    expect($result)->toContain('Test restrictions');

    // Verify no categories are mentioned
    expect($result)->not->toContain('### Category');
    expect($result)->not->toContain('####');
});

it('uses cache correctly', function () {
    // Setup settings
    $settings = app(AiQnaAdvisorSettings::class);
    $settings->instructions = 'Cached instructions';
    $settings->background_information = 'Cached background';
    $settings->restrictions = 'Cached restrictions';
    $settings->save();

    // Create QnaAdvisor
    $qnaAdvisor = QnaAdvisor::factory()->create();

    $category = QnaAdvisorCategory::factory()->create([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Test Category',
        'description' => 'Test description',
    ]);

    QnaAdvisorQuestion::factory()->create([
        'category_id' => $category->getKey(),
        'question' => 'Test question?',
        'answer' => 'Test answer.',
    ]);

    // Execute the action first time
    $action = new GetQnaAdvisorInstructions();
    $result1 = $action->execute($qnaAdvisor);

    // Verify the cache key is used
    $cacheKey = $qnaAdvisor->getInstructionsCacheKey();
    expect($cacheKey)->toBe('qna-advisor-' . $qnaAdvisor->getKey() . '-instructions');

    // Verify the result is cached
    expect(Cache::tags(['{qna_advisor_instructions}'])->has($cacheKey))->toBeTrue();

    // Execute again and verify same result (from cache)
    $result2 = $action->execute($qnaAdvisor);
    expect($result1)->toBe($result2);

    // Clear cache and modify settings
    Cache::tags(['{qna_advisor_instructions}'])->flush();
    $settings->instructions = 'New instructions';
    $settings->save();

    // Execute again and verify new result
    $result3 = $action->execute($qnaAdvisor);
    expect($result3)->toContain('New instructions');
    expect($result3)->not->toBe($result1);
});

it('properly formats markdown with correct spacing', function () {
    // Setup settings
    $settings = app(AiQnaAdvisorSettings::class);
    $settings->instructions = 'Test instructions';
    $settings->background_information = 'Test background';
    $settings->restrictions = 'Test restrictions';
    $settings->save();

    // Create QnaAdvisor with specific content to test spacing
    $qnaAdvisor = QnaAdvisor::factory()->create();

    $category = QnaAdvisorCategory::factory()->create([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Test Category',
        'description' => 'Test description',
    ]);

    QnaAdvisorQuestion::factory()->create([
        'category_id' => $category->getKey(),
        'question' => 'Test question?',
        'answer' => 'Test answer.',
    ]);

    // Execute the action
    $action = new GetQnaAdvisorInstructions();
    $result = $action->execute($qnaAdvisor);

    // Verify proper markdown formatting with spacing
    $lines = explode("\n", $result);

    // Look for the sections without specific whitespace patterns
    $instructionsIndex = null;
    $backgroundIndex = null;
    $qnaIndex = null;
    $restrictionsIndex = null;

    foreach ($lines as $index => $line) {
        if (str_contains($line, '# Instructions')) {
            $instructionsIndex = $index;
        } elseif (str_contains($line, '## Institutional Background Information')) {
            $backgroundIndex = $index;
        } elseif (str_contains($line, '## Questions and Answers')) {
            $qnaIndex = $index;
        } elseif (str_contains($line, '## Restrictions')) {
            $restrictionsIndex = $index;
        }
    }

    expect($instructionsIndex)->not->toBeNull();
    expect($backgroundIndex)->not->toBeNull();
    expect($qnaIndex)->not->toBeNull();
    expect($restrictionsIndex)->not->toBeNull();

    // Verify the order is correct
    expect($instructionsIndex)->toBeLessThan($backgroundIndex);
    expect($backgroundIndex)->toBeLessThan($qnaIndex);
    expect($qnaIndex)->toBeLessThan($restrictionsIndex);

    // Verify there are empty lines for proper spacing
    expect($lines)->toContain('');
});
