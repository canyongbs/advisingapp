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

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionUsersTable;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Livewire\livewire;

it('can see prospect interaction users table', function () {
    $team = Team::factory()->create([
        'name' => 'Test Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    $totalPerUser1 = round((5 / 15) * 100);
    $totalPerUser2 = round((10 / 15) * 100);

    $durationsUser1 = $user1->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), Carbon::DIFF_ABSOLUTE);
    })->filter();

    $avgUser1 = round($durationsUser1->avg());

    $durationsUser2 = $user2->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), Carbon::DIFF_ABSOLUTE);
    })->filter();

    $avgUser2 = round($durationsUser2->avg());

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->assertSee([
            'Super Admin',
            'Computer Operator',
            'Canyon GBS',
            'CEO',
            'Test Team',
            '5',
            '10',
            now()->format('M d, Y'),
            $totalPerUser1 . '%',
            $totalPerUser2 . '%',
            $avgUser1 . ' Min.',
            $avgUser2 . ' Min.',
        ]);
});

it('can filter users by name', function () {
    $team = Team::factory()->create([
        'name' => 'Test Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->filterTable('name', [
            'name' => 'Super Admin',
        ])
        ->assertSee([
            'Super Admin',
        ])
        ->assertDontSee([
            'Canyon GBS',
        ]);
});

it('can filter users by job title', function () {
    $team = Team::factory()->create([
        'name' => 'Test Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->filterTable('job_title', [
            'job_title' => 'Computer Operator',
        ])
        ->assertSee([
            'Computer Operator',
        ])
        ->assertDontSee([
            'CEO',
        ]);
});

it('can filter users by team', function () {
    $team = Team::factory()->create([
        'name' => 'Interaction Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'user1',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->filterTable('team', [
            'team' => $team->getKey(),
        ])
        ->assertSee([
            'Team: Interaction Team',
            'Canyon GBS',
        ]);
    /*->assertDontSee([
        'user1',
    ]);*/
});
