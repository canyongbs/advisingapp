<?php

use Mockery\MockInterface;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Jobs\DeleteExternalAiThread;

it('deletes vector stores for a thread', function () {
    $aiThread = AiThread::factory()
        ->for(
            factory: AiAssistant::factory()->state(['model' => AiModel::OpenAiGpt4o]),
            relationship: 'assistant',
        )
        ->create();

    /** @phpstan-ignore-next-line */
    $this->mock(
        $aiThread->assistant->model->getService()::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('isThreadExisting')->with($aiThread)->once()->andReturn(true)
            ->shouldReceive('deleteThread')->once(),
    );

    (new DeleteExternalAiThread($aiThread))->handle();
});
