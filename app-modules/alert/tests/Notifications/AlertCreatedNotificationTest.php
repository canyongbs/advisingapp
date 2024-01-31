<?php

use App\Models\User;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Alert\Notifications\AlertCreatedNotification;

use function Tests\Helpers\testItIsDispatchedToTheProperChannels;

use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;

testItIsDispatchedToTheProperChannels(
    notification: AlertCreatedNotification::class,
    deliveryChannels: [DatabaseChannel::class],
    triggerNotificationToNotifiable: function () {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $student = Student::factory()->create();

        $student->subscriptions()->create([
            'user_id' => $user->id,
        ]);

        Alert::factory()->create([
            'concern_id' => $student->sisid,
            'concern_type' => Student::class,
        ]);

        return $user;
    }
);
