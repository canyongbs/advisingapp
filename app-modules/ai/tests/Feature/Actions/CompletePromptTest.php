<?php

use Mockery\MockInterface;

use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiFeature;
use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Services\TestAiService;

use function Pest\Laravel\assertDatabaseHas;

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

    $completion = app(CompletePrompt::class)->execute($aiModel, $prompt, $content);

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
