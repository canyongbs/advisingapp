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

    $categoryOne = QnaAdvisorCategory::factory()
        ->for($qnaAdvisor, 'qnaAdvisor')
        ->create([
            'name' => 'Academic Policies',
            'description' => 'Information about academic policies and procedures',
        ]);

    $categoryTwo = QnaAdvisorCategory::factory()
        ->for($qnaAdvisor, 'qnaAdvisor')
        ->create([
            'name' => 'Financial Aid',
            'description' => 'Questions related to financial aid and scholarships',
        ]);

    // Create two questions for each category
    $questionOne = QnaAdvisorQuestion::factory()
        ->for($categoryOne, 'category')
        ->create([
            'question' => 'What is the minimum GPA requirement?',
            'answer' => 'The minimum GPA requirement is 2.0 for undergraduate students.',
        ]);

    $questionTwo = QnaAdvisorQuestion::factory()
        ->for($categoryOne, 'category')
        ->create([
            'question' => 'How do I appeal a grade?',
            'answer' => 'You can appeal a grade by submitting a formal appeal form to the registrar.',
        ]);

    $questionThree = QnaAdvisorQuestion::factory()
        ->for($categoryTwo, 'category')
        ->create([
            'question' => 'When is the FAFSA deadline?',
            'answer' => 'The FAFSA deadline is typically March 1st for the following academic year.',
        ]);

    $questionFour = QnaAdvisorQuestion::factory()
        ->for($categoryTwo, 'category')
        ->create([
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

    $categoryWithQuestions = QnaAdvisorCategory::factory()
        ->for($qnaAdvisor, 'qnaAdvisor')
        ->create([
            'name' => 'Student Services',
            'description' => 'Information about student support services',
        ]);

    $categoryWithoutQuestions = QnaAdvisorCategory::factory()
        ->for($qnaAdvisor, 'qnaAdvisor')
        ->create([
            'name' => 'Campus Recreation',
            'description' => 'Details about recreational activities on campus',
        ]);

    // Create questions only for the first category
    $question = QnaAdvisorQuestion::factory()
        ->for($categoryWithQuestions, 'category')
        ->create([
            'question' => 'Where is the student counseling center located?',
            'answer' => 'The student counseling center is located in the Student Union Building.',
        ]);

    // Execute the action
    $action = new GetQnaAdvisorInstructions();
    $result = $action->execute($qnaAdvisor);

    // Verify the category with questions is included
    expect($result)->toContain('Student Services');
    expect($result)->toContain('Information about student support services');
    expect($result)->toContain('#### Where is the student counseling center located?');
    expect($result)->toContain('The student counseling center is located in the Student Union Building.');

    // Verify the category without questions is NOT included
    expect($result)->not->toContain('Campus Recreation');
    expect($result)->not->toContain('Details about recreational activities on campus');

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

    $category = QnaAdvisorCategory::factory()
        ->for($qnaAdvisor, 'qnaAdvisor')
        ->create([
            'name' => 'Library Resources',
            'description' => 'Information about library services and resources',
        ]);

    $question = QnaAdvisorQuestion::factory()
        ->for($category, 'category')
        ->create([
            'question' => 'What are the library hours?',
            'answer' => 'The library is open Monday through Friday from 8am to 10pm.',
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
    expect($result)->toContain('Library Resources');
    expect($result)->toContain('#### What are the library hours?');
    expect($result)->toContain('The library is open Monday through Friday from 8am to 10pm.');
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

    $category = QnaAdvisorCategory::factory()
        ->for($qnaAdvisor, 'qnaAdvisor')
        ->create([
            'name' => 'Technology Support',
            'description' => 'Information about IT help and computer resources',
        ]);

    $question = QnaAdvisorQuestion::factory()
        ->for($category, 'category')
        ->create([
            'question' => 'How do I reset my password?',
            'answer' => 'You can reset your password by visiting the IT help desk in the main building.',
        ]);

    // Execute the action first time
    $action = new GetQnaAdvisorInstructions();
    $resultOne = $action->execute($qnaAdvisor);

    // Verify the cache key is used
    $cacheKey = $qnaAdvisor->getInstructionsCacheKey();
    expect($cacheKey)->toBe('qna-advisor-' . $qnaAdvisor->getKey() . '-instructions');

    // Verify the result is cached
    expect(Cache::tags(['{qna_advisor_instructions}'])->has($cacheKey))->toBeTrue();

    // Execute again and verify same result (from cache)
    $resultTwo = $action->execute($qnaAdvisor);
    expect($resultOne)->toBe($resultTwo);

    // Clear cache and modify settings
    Cache::tags(['{qna_advisor_instructions}'])->flush();
    $settings->instructions = 'New instructions';
    $settings->save();

    // Execute again and verify new result
    $resultThree = $action->execute($qnaAdvisor);
    expect($resultThree)->toContain('New instructions');
    expect($resultThree)->not->toBe($resultOne);
});
