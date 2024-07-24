<?php

use Mockery\MockInterface;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\Ai\Listeners\DeleteAiThreadVectorStores;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Threads\ThreadsDataTransferObject;

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
            ->shouldReceive('supportsMessageFileUploads')->once()->andReturn(true)
            ->shouldReceive('retrieveThread')->once()->andReturn(ThreadsDataTransferObject::from([
                'id' => 1,
                'vectorStoreIds' => [1, 2, 3],
            ]))
            ->shouldReceive('deleteVectorStore')->times(3),
    );

    (new DeleteAiThreadVectorStores())->handle(new AiThreadForceDeleting($aiThread));
});
