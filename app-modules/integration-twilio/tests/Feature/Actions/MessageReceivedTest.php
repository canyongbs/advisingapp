<?php

use function Tests\loadFixtureFromModule;

use Assist\AssistDataModel\Models\Student;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Assist\IntegrationTwilio\Actions\MessageReceived;

it('will not create an engagement response when it cannot find an associated message sender', function () {
    $messageData = loadFixtureFromModule('integration-twilio', 'MessageReceived/payload');

    $messageReceived = new MessageReceived($messageData);
    $messageReceived->handle();

    assertDatabaseMissing('engagement_responses', [
        'content' => $messageData['Body'],
    ]);
});

it('will create an engagement response when a message is received', function () {
    $messageData = loadFixtureFromModule('integration-twilio', 'MessageReceived/payload');

    $student = Student::factory()->create();

    $messageData['From'] = $student->mobile;

    $messageReceived = new MessageReceived($messageData);

    $messageReceived->handle();

    assertDatabaseHas('engagement_responses', [
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'content' => $messageData['Body'],
    ]);
});
