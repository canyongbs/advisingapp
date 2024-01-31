<?php

use App\Models\User;

use function Pest\Laravel\seed;

use AdvisingApp\Application\Models\ApplicationSubmission;

use function Tests\Helpers\testItIsDispatchedToTheProperChannels;

use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;
use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Notifications\AuthorLinkedApplicationSubmissionCreatedNotification;

testItIsDispatchedToTheProperChannels(
    notification: AuthorLinkedApplicationSubmissionCreatedNotification::class,
    deliveryChannels: [DatabaseChannel::class],
    triggerNotificationToNotifiable: function () {
        seed(ApplicationSubmissionStateSeeder::class);

        $submission = ApplicationSubmission::factory()->make();

        $user = User::factory()->create();

        $user->subscriptions()->create([
            'subscribable_id' => $submission->author->getKey(),
            'subscribable_type' => $submission->author->getMorphClass(),
        ]);

        $submission->save();

        return $user;
    }
);
