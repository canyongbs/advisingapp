<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Mockery\MockInterface;

use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiFeature;
use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\Ai\Models\LegacyAiMessageLog;

it('calls the passed AI Models service class complete method', function () {
    asSuperAdmin();

    $aiModel = AiModel::Test;
    $prompt = 'test-prompt';
    $content = 'test-content';

    /** @phpstan-ignore-next-line */
    $this->mock(
        TestAiService::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('complete')
            ->once()
            ->with($prompt, $content)
            ->andReturn('test-completion'),
    );

    $completion = app(CompletePrompt::class)->execute($aiModel, $prompt, $content);

    expect($completion)->toBe('test-completion');
});

it('creates a new AI message log', function () {
    asSuperAdmin();

    $aiModel = AiModel::Test;
    $prompt = 'test-prompt';
    $content = 'test-content';

    expect(LegacyAiMessageLog::count())->toBe(0);

    /** @phpstan-ignore-next-line */
    $this->mock(
        TestAiService::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('complete')
            ->once()
            ->with($prompt, $content)
            ->andReturn('test-completion'),
    );

    app(CompletePrompt::class)->execute($aiModel, $prompt, $content);

    expect(LegacyAiMessageLog::count())->toBe(1);

    expect(LegacyAiMessageLog::first())
        ->message->toBe($content)
        ->metadata->toBe([
            'prompt' => $prompt,
            'completion' => 'test-completion',
        ])
        ->user_id->toBe(auth()->id())
        ->ai_assistant_name->toBe('Institutional Assistant')
        ->feature->toBe(AiFeature::DraftWithAi);
});
