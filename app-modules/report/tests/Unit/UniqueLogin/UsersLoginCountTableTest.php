<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Report\Filament\Widgets\UsersLoginCountTable;
use App\Models\User;

use function Pest\Livewire\livewire;

it('can filter users based they have ever logged in or not', function () {
  $loggedUsers = User::factory()->count(3)->create([
    'first_login_at' => now()->subDays(1)
  ]);

  $notLoggedUsers = User::factory()->count(3)->create([
    'first_login_at' => null
  ]);

  livewire(UsersLoginCountTable::class, ['cacheTag' => 'report-users'])
    ->filterTable('has_logged_in', 'logged_in')
    ->assertCanSeeTableRecords($loggedUsers)
    ->assertCanNotSeeTableRecords($notLoggedUsers)
    ->filterTable('has_logged_in', 'never_logged_in')
    ->assertCanSeeTableRecords($notLoggedUsers)
    ->assertCanNotSeeTableRecords($loggedUsers);
});

it('can filter users to get users who first logged in between selected dates', function () {
  $lastDayUsers = User::factory(2)->create([
    'first_login_at' => now()->subDays(1)
  ]);

  $recentUsers = User::factory(2)->create([
    'first_login_at' => now()->subMonth(2)
  ]);

  livewire(UsersLoginCountTable::class, ['cacheTag' => 'report-users'])
    ->filterTable('first_login_at', [
      'first_logged_in_from' => now()->subDays(1)->toDateString(),
      'first_logged_in_until' => now()->toDateString(),
    ])
    ->assertCanSeeTableRecords($lastDayUsers)
    ->assertCanNotSeeTableRecords($recentUsers)
    ->filterTable('first_login_at', [
      'first_logged_in_from' => now()->subMonth(2)->toDateString(),
      'first_logged_in_until' => now()->subMonth(1)->toDateString(),
    ])
    ->assertCanSeeTableRecords($recentUsers)
    ->assertCanNotSeeTableRecords($lastDayUsers);
});

it('can filter users to get users who last logged in between selected dates', function () {
  $lastDayUsers = User::factory(2)->create([
    'last_logged_in_at' => now()->subDays(1)
  ]);

  $recentUsers = User::factory(2)->create([
    'last_logged_in_at' => now()->subMonth(2)
  ]);

  livewire(UsersLoginCountTable::class, ['cacheTag' => 'report-users'])
    ->filterTable('last_logged_in_at', [
      'last_logged_in_from' => now()->subDays(1)->toDateString(),
      'last_logged_in_until' => now()->toDateString(),
    ])
    ->assertCanSeeTableRecords($lastDayUsers)
    ->assertCanNotSeeTableRecords($recentUsers)
    ->filterTable('last_logged_in_at', [
      'last_logged_in_from' => now()->subMonth(2)->toDateString(),
      'last_logged_in_until' => now()->subMonth(1)->toDateString(),
    ])
    ->assertCanSeeTableRecords($recentUsers)
    ->assertCanNotSeeTableRecords($lastDayUsers);
});
