<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Notification\Events\SubscriptionCreated;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ViewProspect;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Filament\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

it('dispatches a notification to subscribed users when an engagement response is created', function () {
    Notification::fake();
    Event::fake([SubscriptionCreated::class]);

    /** @var EngagementResponse $response */
    $response = EngagementResponse::factory()->make();

    /** @var Student|Prospect $educatable */
    $educatable = $response->sender;

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $educatable->subscribedUsers()->attach($user);

    $response->save();

    Notification::assertSentTo(
        $user,
        DatabaseNotification::class,
        function (DatabaseNotification $notification) use ($response, $educatable) {
            $type = match ($response->type) {
                EngagementResponseType::Email => 'email',
                EngagementResponseType::Sms => 'text message',
            };

            $title = match (true) {
                $educatable instanceof Student => "An inbound {$type} has been received for student <a href='" . ViewStudent::getUrl(['record' => $educatable]) . "' target='_blank' class='underline'>{$educatable[$educatable->displayNameKey()]}</a> and has been placed in a new status.",
                $educatable instanceof Prospect => "An inbound {$type} has been received for prospect <a href='" . ViewProspect::getUrl(['record' => $educatable]) . "' target='_blank' class='underline'>{$educatable->full_name}</a> and has been placed in a new status.",
            };

            return $notification->data['title'] === $title;
        },
    );
});