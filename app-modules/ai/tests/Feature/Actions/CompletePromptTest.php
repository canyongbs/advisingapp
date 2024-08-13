<?php

use Mockery\MockInterface;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Services\TestAiService;

use function Tests\asSuperAdmin;

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
