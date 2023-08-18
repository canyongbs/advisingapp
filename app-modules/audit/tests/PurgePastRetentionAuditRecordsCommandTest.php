<?php

use Assist\Audit\Models\Audit;

use function Pest\Laravel\artisan;
use function Pest\Laravel\travelTo;

use Illuminate\Support\Facades\Event;
use Assist\Audit\Settings\AuditSettings;
use Illuminate\Console\Events\ScheduledTaskStarting;

test('PurgePastRetentionAuditRecordsCommand properly deletes records', function () {
    $auditSettings = resolve(AuditSettings::class);
    $auditSettings->retention_duration = 90;
    $auditSettings->save();

    $retainedAudits = Audit::factory()
        ->count(5)
        ->create(
            [
                'created_at' => now()->subDays(30),
            ]
        );

    $purgedAudits = Audit::factory()
        ->count(5)
        ->create(
            [
                'created_at' => now()->subDays(100),
            ]
        );

    $auditCount = Audit::count();

    artisan('audit:purge-past-retention-audit-records');

    expect(Audit::count())->toBe($auditCount - $purgedAudits->count());

    $retainedAudits->each(
        function ($audit) {
            expect(Audit::where('id', $audit->id)->exists())->toBeTrue();
        }
    );

    $purgedAudits->each(
        function ($audit) {
            expect(Audit::where('id', $audit->id)->exists())->toBeFalse();
        }
    );
});

test('PurgePastRetentionAuditRecordsCommand is properly scheduled', function () {
    Event::fake();

    travelTo(now()->startOfDay());

    artisan('schedule:run');

    Event::assertDispatched(function (ScheduledTaskStarting $event) {
        return str($event->task->command)->contains('audit:purge-past-retention-audit-records');
    });
});
