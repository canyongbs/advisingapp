<?php

use OpenAI\Resources\Files;

use function Tests\asSuperAdmin;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiMessageFile;
use OpenAI\Responses\Files\DeleteResponse;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4oService;

it('can delete a file', function () {
    asSuperAdmin();

    /** @var OpenAiGpt4oService $service */
    $service = AiModel::OpenAiGpt4o->getService();

    $service->fake();

    /** @var ClientFake $client */
    $client = $service->getClient();

    $client->addResponses([
        DeleteResponse::fake(),
    ]);

    $aiMessageFile = AiMessageFile::factory()
        ->make();

    $service->beforeMessageFileForceDeleted($aiMessageFile);

    $client->assertSent(Files::class, 1);
});
