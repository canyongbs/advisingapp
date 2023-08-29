<?php

use Assist\AssistDataModel\Models\Student;
use Assist\IntegrationTwilio\Actions\MessageReceived;
use Assist\Engagement\Exceptions\UnknownEngagementSenderException;

it('will_throw_an_exception_when_it_cannot_find_an_associated_message_sender', function () {
    $messageData = $this->loadFixtureFromModule('integration-twilio', 'MessageReceived/payload');

    $this->expectException(UnknownEngagementSenderException::class);

    $messageReceived = new MessageReceived($messageData);
    $messageReceived->handle();
});

it('will create an engagement response when a message is received', function () {
    $messageData = $this->loadFixtureFromModule('integration-twilio', 'MessageReceived/payload');

    $student = Student::factory()->create();

    $messageData['from'] = $student->mobile;

    $messageReceived = new MessageReceived($messageData);

    $messageReceived->handle();

    $this->assertDatabaseHas('engagement_responses', [
        'sender_id' => $student->id,
        'sender_type' => (new Student())->getMorphClass(),
        'content' => $messageData['body'],
    ]);
});
