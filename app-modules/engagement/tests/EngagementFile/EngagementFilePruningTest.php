<?php

use function Pest\Laravel\artisan;

use Illuminate\Console\Scheduling\Schedule;
use Assist\Engagement\Models\EngagementFile;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;

use Illuminate\Database\Console\PruneCommand;

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

    $events = collect($schedule->events())->filter(function (Illuminate\Console\Scheduling\Event $event) {
        $engagementFileClass = EngagementFile::class;

        return str_contains($event->command, "model:prune --model='{$engagementFileClass}'")
            && $event->expression === '0 0 * * *';
    });

    expect($events)->toHaveCount(1);
});
