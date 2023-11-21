<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
