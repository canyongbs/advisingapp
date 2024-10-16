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

use function Pest\Laravel\artisan;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;

use Illuminate\Database\Console\PruneCommand;
use AdvisingApp\Engagement\Models\EngagementFile;

it('correctly prunes EngagementFiles based on retention_date', function () {
    $expiredFile = EngagementFile::factory()->create([
        'retention_date' => fake()->dateTimeBetween('-1 year', '-1 day'),
    ]);

    $noRetentionDateFile = EngagementFile::factory()->create([
        'retention_date' => null,
    ]);

    $futureRetentionDateFile = EngagementFile::factory()->create([
        'retention_date' => fake()->dateTimeBetween('+1 day', '+ 1 year'),
    ]);

    artisan(PruneCommand::class, [
        '--model' => EngagementFile::class,
    ])->assertExitCode(0);

    assertModelMissing($expiredFile);
    assertModelExists($noRetentionDateFile);
    assertModelExists($futureRetentionDateFile);
});

it('is scheduled to prune EngagementFiles daily during scheduler run', function () {
    $schedule = app()->make(Schedule::class);

    $events = collect($schedule->events())->filter(function (Event $event) {
        $engagementFileClass = EngagementFile::class;

        return str_contains($event->command, "model:prune --model={$engagementFileClass}")
            && $event->expression === '0 0 * * *';
    });

    expect($events)->toHaveCount(1);
});
