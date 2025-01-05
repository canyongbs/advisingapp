<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Report\Filament\Widgets\UsersStats;
use AdvisingApp\Report\Jobs\RecordUserUniqueLoginTrackedEvent;
use App\Models\User;

it('Check total users', function () {
  $userCount = rand(1, 10);
  $usersStats = new UsersStats();
  $usersStats->cacheTag = 'users_stats';

  User::factory()->count($userCount)->create();
  $stats = $usersStats->getStats();
  $totalUsersStat = $stats[0];
  expect($totalUsersStat->getValue())->toEqual($userCount);
});

it('Check new users who were created within one month', function () {
  $usersStats = new UsersStats();
  $usersStats->cacheTag = 'users_stats';

  User::factory()->count(3)->create([
    'created_at' => now()->subMonths(2),
  ]);
  User::factory()->count(3)->create([
    'created_at' => now()->subDays(rand(1, 30)),
  ]);

  $stats = $usersStats->getStats();
  $newUsersStat = $stats[1];
  expect($newUsersStat->getValue())->toEqual(3);
});

it('Check total users with unique login event type', function () {
  $userCount = rand(1, 10);
  $usersStats = new UsersStats();
  $usersStats->cacheTag = 'users_stats';
  $logins = 0;

  User::factory()->count($userCount)->create()->each(function ($user) use (&$logins) {
    $randomLogins = rand(1, 10);

    for ($i = 0; $i < $randomLogins; $i++) {
      dispatch(new RecordUserUniqueLoginTrackedEvent(
        occurredAt: now(),
        user: $user,
      ));
      $logins++;
    }
  });

  $stats = $usersStats->getStats();
  $totalUsersWithUniqueLoginStat = $stats[2];
  expect($totalUsersWithUniqueLoginStat->getValue())->toEqual($logins);
});
