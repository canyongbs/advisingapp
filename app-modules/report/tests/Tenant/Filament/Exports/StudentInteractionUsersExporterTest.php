<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Report\Filament\Exports\StudentInteractionUsersExporter;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Carbon\Carbon;

it('exports only interactions with students who are not archived, within the reported date range', function (): void {
    $active = Student::factory()->create();
    $archived = Student::factory()->create();

    $user = User::factory()->create();

    Interaction::factory()->for($active, 'interactable')->for($user, 'user')->create([
        'created_at' => Carbon::parse('2026-03-10'),
        'start_datetime' => Carbon::parse('2026-03-10 09:00:00'),
        'end_datetime' => Carbon::parse('2026-03-10 09:10:00'),
    ]);
    Interaction::factory()->for($active, 'interactable')->for($user, 'user')->create([
        'created_at' => Carbon::parse('2026-03-20'),
        'start_datetime' => Carbon::parse('2026-03-20 09:00:00'),
        'end_datetime' => Carbon::parse('2026-03-20 09:20:00'),
    ]);
    Interaction::factory()->for($active, 'interactable')->for($user, 'user')->create([
        'created_at' => Carbon::parse('2026-05-10'),
        'start_datetime' => Carbon::parse('2026-05-10 09:00:00'),
        'end_datetime' => Carbon::parse('2026-05-10 10:00:00'),
    ]);
    Interaction::factory()->for($archived, 'interactable')->for($user, 'user')->create([
        'created_at' => Carbon::parse('2026-03-15'),
        'start_datetime' => Carbon::parse('2026-03-15 09:00:00'),
        'end_datetime' => Carbon::parse('2026-03-15 10:40:00'),
    ]);

    $archived->archive();

    $row = StudentInteractionUsersExporter::test(options: [
        'startDate' => '2026-03-01',
        'endDate' => '2026-03-31',
    ])->export($user);

    // Name, First, Most Recent, Total, Total %, Avg. Duration.
    expect($row[1])->toBe('Mar 10, 2026')
        ->and($row[2])->toBe('Mar 20, 2026')
        ->and($row[3])->toEqual(2)
        // The share is out of every interaction with students who are not archived.
        ->and($row[4])->toBe('67%')
        // Only the two in-range interactions (10 and 20 minutes) are averaged.
        ->and($row[5])->toBe('15 Min.');
});

it('exports every interaction with students who are not archived when no date range was reported', function (): void {
    $active = Student::factory()->create();
    $archived = Student::factory()->create();

    $user = User::factory()->create();

    Interaction::factory()->for($active, 'interactable')->for($user, 'user')->create([
        'created_at' => Carbon::parse('2026-03-10'),
        'start_datetime' => Carbon::parse('2026-03-10 09:00:00'),
        'end_datetime' => Carbon::parse('2026-03-10 09:10:00'),
    ]);
    Interaction::factory()->for($active, 'interactable')->for($user, 'user')->create([
        'created_at' => Carbon::parse('2026-05-10'),
        'start_datetime' => Carbon::parse('2026-05-10 09:00:00'),
        'end_datetime' => Carbon::parse('2026-05-10 10:00:00'),
    ]);
    Interaction::factory()->for($archived, 'interactable')->for($user, 'user')->create([
        'created_at' => Carbon::parse('2026-03-15'),
        'start_datetime' => Carbon::parse('2026-03-15 09:00:00'),
        'end_datetime' => Carbon::parse('2026-03-15 10:40:00'),
    ]);

    $archived->archive();

    $row = StudentInteractionUsersExporter::test()->export($user);

    expect($row[1])->toBe('Mar 10, 2026')
        ->and($row[2])->toBe('May 10, 2026')
        ->and($row[3])->toEqual(2)
        ->and($row[4])->toBe('100%')
        // The archived student's 100 minute interaction is left out of the average.
        ->and($row[5])->toBe('35 Min.');
});
