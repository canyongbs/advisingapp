<?php

use App\Models\User;
use Assist\Alert\Models\Alert;

use function Pest\Laravel\actingAs;

use Assist\AssistDataModel\Models\Student;
use Illuminate\Support\Facades\Notification;
use Assist\Alert\Notifications\AlertCreatedNotification;

it('creates a subscription for the user that created the Alert', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect($user->subscriptions->count())->toEqual(0);

    $alert = Alert::factory()->create();

    $user->refresh();

    expect($user->subscriptions->first()->subscribable)->toEqual($alert->concern);
});

it('dispatches the proper notifications to subscribers on created', function () {
    Notification::fake();

    $users = User::factory()->count(5)->create();

    /** @var Student $student */
    $student = Student::factory()->create();

    $student->subscriptions()->createMany($users->map(fn (User $user) => [
        'user_id' => $user->id,
    ])->toArray());

    Alert::factory()->create([
        'concern_id' => $student->sisid,
        'concern_type' => Student::class,
    ]);

    $student->refresh();

    Notification::assertSentTo($users, AlertCreatedNotification::class);
    Notification::assertSentTimes(AlertCreatedNotification::class, $student->subscriptions()->count());
});
