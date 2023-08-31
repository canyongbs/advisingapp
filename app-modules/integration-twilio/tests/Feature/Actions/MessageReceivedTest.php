<?php

use Assist\AssistDataModel\Models\Student;
use Assist\IntegrationTwilio\Actions\MessageReceived;

it('will not create an engagement response when it cannot find an associated message sender', function () {
    $messageData = $this->loadFixtureFromModule('integration-twilio', 'MessageReceived/payload');

    $messageReceived = new MessageReceived($messageData);
    $messageReceived->handle();

    $this->assertDatabaseMissing('engagement_responses', [
        'content' => $messageData['Body'],
    ]);
});

it('will create an engagement response when a message is received', function () {
    $messageData = $this->loadFixtureFromModule('integration-twilio', 'MessageReceived/payload');

    $student = Student::factory()->create();

    $messageData['From'] = $student->mobile;

    $messageReceived = new MessageReceived($messageData);

    $messageReceived->handle();

    $this->assertDatabaseHas('engagement_responses', [
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'content' => $messageData['Body'],
    ]);
});
